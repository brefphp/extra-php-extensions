SHELL := /bin/bash
php_versions = 72 73 74

docker-images:
	PWD=pwd
	for dir in layers/*; do \
		for php_version in $(php_versions); do \
			echo "###############################################"; \
			echo "###############################################"; \
			echo "### Building $${dir} PHP$${php_version}"; \
			echo "###"; \
			cd ${PWD} ; cd $${dir} ; docker build -t bref/$${dir}-php-$${php_version} --build-arg PHP_VERSION=$${php_version} ${DOCKER_BUILD_FLAGS} . ; \
			echo ""; \
		done \
	done

# The PHP runtimes
export: docker-images
	PWD=pwd
	rm -rf export/tmp || true
	mkdir export/tmp
	for dir in layers/*; do \
		for php_version in $(php_versions); do \
			echo "###############################################"; \
			echo "###############################################"; \
			echo "### Exporting $${dir} PHP$${php_version}"; \
			echo "###"; \
			cd ${PWD} ; cd export/tmp ; \
			docker run --entrypoint "tar" bref/$${dir}-php-$${php_version} -ch -C /opt . | tar -x;zip -X --quiet --recurse-paths ../`echo "$${dir}-php-$${php_version}" | sed -e "s/layers\//layer-/g"`.zip . ; \
			echo ""; \
		done \
	done
	rm -rf export/tmp

publish: export
	php src/publish.php