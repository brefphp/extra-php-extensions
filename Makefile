SHELL := /bin/bash
php_versions = 72 73 74
layer = *


docker-images:
	PWD=pwd
	set -e; \
	for dir in layers/${layer}; do \
		for php_version in $(php_versions); do \
			echo "###############################################"; \
			echo "###############################################"; \
			echo "### Building $${dir} PHP$${php_version}"; \
			echo "###"; \
			cd ${PWD} ; cd $${dir} ; \
			docker build -t bref/$${dir}-php-$${php_version} --build-arg PHP_VERSION=$${php_version} ${DOCKER_BUILD_FLAGS} . ; \
			echo ""; \
		done \
	done

# The PHP runtimes
layers: docker-images
	PWD=pwd
	rm -rf export/layer-${layer}.zip || true
	mkdir export/tmp
	set -e; \
	for dir in layers/${layer}; do \
		for php_version in $(php_versions); do \
			echo "###############################################"; \
			echo "###############################################"; \
			echo "### Exporting $${dir} PHP$${php_version}"; \
			echo "###"; \
			cd ${PWD} ; rm -rf export/tmp/${layer} || true ; cd export/tmp ; \
			docker run --entrypoint "tar" bref/$${dir}-php-$${php_version} -ch -C /opt . | tar -x ; \
			zip --quiet -X --recurse-paths ../`echo "$${dir}-php-$${php_version}" | sed -e "s/layers\//layer-/g"`.zip . ; \
			echo ""; \
		done \
	done
	rm -rf export/tmp

publish: layers
	php ./bref-extra publish
	php ./bref-extra list

# Publish doocker images
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
			  echo "Pusing tagged images"; \
			  docker tag $$privateImage:latest $$publicImage:${DOCKER_TAG}; \
			  docker push $$publicImage:${DOCKER_TAG}; \
			fi; \
			echo ""; \
		done \
	done


