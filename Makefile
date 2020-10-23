SHELL := /bin/bash
php_versions ?= 72 73 74
layer ?= *
parallel = $(if $(shell which parallel),true,false)

define generate_list
	for dir in layers/${layer}; do \
		for php_version in $(php_versions); do \
		    echo "$${dir} $${php_version}"; \
		done \
	done
endef

define build_docker_image
	docker build -t bref/${1}-php-${2} --build-arg PHP_VERSION=${2} ${DOCKER_BUILD_FLAGS} ${1}
endef

docker-images:
	if $(parallel); then \
		$(call generate_list) | parallel --colsep ' ' $(call build_docker_image,{1},{2}) ; \
	else  \
		set -e; \
		for dir in layers/${layer}; do \
			for php_version in $(php_versions); do \
				echo "###############################################"; \
				echo "###############################################"; \
				echo "### Building $${dir} PHP$${php_version}"; \
				echo "###"; \
				$(call build_docker_image,$${dir},$${php_version}) ; \
				echo ""; \
			done \
		done \
	fi;

# The PHP runtimes
layers: docker-images
	PWD=pwd
	rm -rf export/layer-${layer}.zip || true
	mkdir -p export/tmp
	set -e; \
	for dir in layers/${layer}; do \
		for php_version in $(php_versions); do \
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
		for php_version in $(php_versions); do \
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

