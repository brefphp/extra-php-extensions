SHELL := /bin/bash
layer ?= *
parallel ?= $(if $(shell which parallel),true,false)
resolve_php_versions = $(or $(php_versions),`jq -r '.php | join(" ")' ${1}/config.json`)
resolve_tags = `./new-docker-tags.php $(DOCKER_TAG)`

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
	if [ "${layer}" != "*" ]; then test -d layers/${layer}; fi
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
	if [ "${layer}" != "*" ]; then test -d layers/${layer}; fi
	set -e; \
	for dir in layers/${layer}; do \
		for php_version in $(call resolve_php_versions,$${dir}); do \
			echo "###############################################"; \
			echo "###############################################"; \
			echo "### Testing $${dir} PHP$${php_version}"; \
			echo "###"; \
			docker build --build-arg PHP_VERSION=$${php_version} --build-arg TARGET_IMAGE=$${dir}-php-$${php_version} -t bref/test-$${dir}-$${php_version} tests ; \
			docker run --rm -v $$(pwd)/$${dir}:/var/task bref/test-$${dir}-$${php_version} /opt/bin/php /var/task/test.php ; \
			if docker run --rm -v $$(pwd)/$${dir}:/var/task bref/test-$${dir}-$${php_version} /opt/bin/php -v 2>&1 >/dev/null | grep -q 'Unable\|Warning'; then exit 1; fi ; \
			echo " - Test passed"; \
		done \
	done;

# The PHP runtimes
layers: docker-images
	if [ "${layer}" != "*" ]; then test -d layers/${layer}; fi
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
			if (test $(DOCKER_TAG)); then \
			  echo "Pushing tagged images"; \
			  for tag in $(call resolve_tags); do \
			    echo ""; \
			    echo "docker push $$publicImage:$${tag}"; \
			    docker tag $$privateImage:latest $$publicImage:$${tag}; \
			    docker push $$publicImage:$${tag}; \
			  done; \
			fi; \
			echo ""; \
		done \
	done

