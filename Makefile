SHELL := /bin/bash
layer ?= *
parallel = $(if $(shell which parallel),true,false)
resolve_php_versions = $(or $(php_versions),`jq -r '.php | join(" ")' ${1}/config.json`)

define generate_list
	for dir in layers/${layer}; do \
		for php_version in $(call resolve_php_versions,$${dir}); do \
		    echo "$${dir} $${php_version}"; \
		done \
	done
endef

define build_docker_image
	docker build -t bref/${1}-php-${2} --build-arg PHP_VERSION=${2} ${DOCKER_BUILD_FLAGS} ${1}
endef

docker-images:
	test -d layers/${layer}
	if $(parallel); then \
		$(call generate_list) | parallel --colsep ' ' $(call build_docker_image,{1},{2}) ; \
	else  \
		set -e; \
		for dir in layers/${layer}; do \
			for php_version in $(call resolve_php_versions,$${dir}); do \
				echo "###############################################"; \
				echo "###############################################"; \
				echo "### Building $${dir} PHP$${php_version}"; \
				echo "###"; \
				$(call build_docker_image,$${dir},$${php_version}) ; \
				echo ""; \
			done \
		done \
	fi;

test: docker-images
	test -d layers/${layer}
	set -e; \
	for dir in layers/${layer}; do \
		for php_version in $(call resolve_php_versions,$${dir}); do \
			echo "###############################################"; \
			echo "###############################################"; \
			echo "### Testing $${dir} PHP$${php_version}"; \
			echo "###"; \
			docker build --build-arg PHP_VERSION=$${php_version} --build-arg TARGET_IMAGE=$${dir}-php-$${php_version} -t bref/test-$${layer}-$${php_version} tests ; \
			echo "docker run --rm -v $$(pwd)/layers/$${layer}:/var/task bref/test-$${layer}-$${php_version} /opt/bin/php /var/task/test.php" ; \
			docker run --rm -v $$(pwd)/layers/$${layer}:/var/task bref/test-$${layer}-$${php_version} /opt/bin/php /var/task/test.php ; \
			if docker run --rm -v $$(pwd)/layers/$${layer}:/var/task bref/test-$${layer}-$${php_version} /opt/bin/php -v 2>&1 >/dev/null | grep -q 'Unable\|Warning'; then exit 1; fi ; \
			echo ""; \
		done \
	done;

# The PHP runtimes
layers: docker-images
	test -d layers/${layer}
	PWD=pwd
	rm -rf export/layer-${layer}.zip || true
	mkdir -p export/tmp
	set -e; \
	for dir in layers/${layer}; do \
		for php_version in $(call resolve_php_versions,${PWD}/$${dir}); do \
			echo "###############################################"; \
			echo "###############################################"; \
			echo "### Exporting $${dir} PHP$${php_version}"; \
			echo "###"; \
			cd ${PWD} ; rm -rf export/tmp/${layer} || true ; cd export/tmp ; \
			CID=$$(docker create --entrypoint=scratch bref/$${dir}-php-$${php_version}) ; \
			docker cp $${CID}:/opt . ; \
			docker rm $${CID} ; \
			cd ./opt ; \
			zip --quiet -X --recurse-paths ../../`echo "$${dir}-php-$${php_version}" | sed -e "s/layers\//layer-/g"`.zip . ; \
			echo ""; \
		done \
	done
	rm -rf export/tmp

clean:
	rm -f export/layer-*

publish: layers
	php ./bref-extra publish
	php ./bref-extra list

# Publish docker images
publish-docker-images: docker-images
	for dir in layers/${layer}; do \
		for php_version in $(call resolve_php_versions,$${dir}); do \
			echo "###############################################"; \
			echo "###############################################"; \
			echo "### Publishing $${dir} PHP$${php_version}"; \
			echo "###"; \
			privateImage="bref/$${dir}-php-$${php_version}"; \
			publicImage=$${privateImage/layers\//extra-}; \
			echo "Image name: $$publicImage"; \
			docker tag $$privateImage:latest $$publicImage:latest ; \
			docker push $$publicImage:latest; \
			if (test $(DOCKER_TAG)); then \
			  echo "Pushing tagged images"; \
			  docker tag $$privateImage:latest $$publicImage:${DOCKER_TAG}; \
			  docker push $$publicImage:${DOCKER_TAG}; \
			fi; \
			echo ""; \
		done \
	done

