SHELL := /bin/bash
cpu ?= x86
layer ?= *
resolve_php_versions = $(or $(php_versions),`jq -r '.php | join(" ")' ${1}/config.json`)
resolve_tags = `./new-docker-tags.php $(DOCKER_TAG)`
BREF_VERSION = 2

ifeq ($(cpu), arm) # if $CPU=="arm"
  export cpu = arm-
  export cpu_prefix = arm-
  export docker_prefix = arm-
  export archive_prefix = arm-
else
  export cpu = x86
  export cpu_prefix =
  export docker_prefix =
  export archive_prefix =
endif

define build_docker_image
	docker build -t bref/${1}-${docker_prefix}php-${2} --build-arg CPU_PREFIX=${cpu_prefix} --build-arg PHP_VERSION=${2} --build-arg BREF_VERSION=${BREF_VERSION} ${DOCKER_BUILD_FLAGS} ${1}
endef

docker-images:
	if [ "${layer}" != "*" ]; then test -d layers/${layer}; fi
	set -e; \
	for dir in layers/${layer}; do \
		for php_version in $(call resolve_php_versions,$${dir}); do \
			echo "###############################################"; \
			echo "###############################################"; \
			echo "### Building $${dir} PHP$${php_version} $${cpu}"; \
			echo "###"; \
			$(call build_docker_image,$${dir},$${php_version}) ; \
			echo ""; \
		done \
	done

test: docker-images
	if [ "${layer}" != "*" ]; then test -d layers/${layer}; fi
	set -e; \
	for dir in layers/${layer}; do \
		for php_version in $(call resolve_php_versions,$${dir}); do \
			echo "###############################################"; \
			echo "###############################################"; \
			echo "### Testing $${dir} PHP$${php_version} $${cpu}"; \
			echo "###"; \
			docker build --build-arg CPU_PREFIX=${cpu_prefix} --build-arg PHP_VERSION=$${php_version} --build-arg BREF_VERSION=${BREF_VERSION} --build-arg TARGET_IMAGE=$${dir}-${docker_prefix}php-$${php_version} -t bref/test-$${dir}-${cpu_prefix}$${php_version} tests ; \
			docker run --entrypoint= --rm -v $$(pwd)/$${dir}:/var/task bref/test-$${dir}-${cpu_prefix}$${php_version} /opt/bin/php /var/task/test.php ; \
			if docker run --entrypoint= --rm -v $$(pwd)/$${dir}:/var/task bref/test-$${dir}-${cpu_prefix}$${php_version} /opt/bin/php -v 2>&1 >/dev/null | grep -q 'Unable\|Warning'; then exit 1; fi ; \
			echo ""; \
			echo " - Test passed"; \
			echo ""; \
		done \
	done;

# The PHP runtimes
layers: docker-images
	if [ "${layer}" != "*" ]; then test -d layers/${layer}; fi
	PWD=pwd
	rm -rf export/${archive_prefix}layer-${layer}.zip || true
	mkdir -p export/tmp
	set -e; \
	for dir in layers/${layer}; do \
		for php_version in $(call resolve_php_versions,${PWD}/$${dir}); do \
			echo "###############################################"; \
			echo "###############################################"; \
			echo "### Exporting $${dir} PHP$${php_version} $${cpu}"; \
			echo "###"; \
			cd ${PWD} ; rm -rf export/tmp/${layer} || true ; cd export/tmp ; \
			CID=$$(docker create --entrypoint=scratch bref/$${dir}-${docker_prefix}php-$${php_version}) ; \
			docker cp $${CID}:/opt . ; \
			docker rm $${CID} ; \
			cd ./opt ; \
			zip --quiet -X --recurse-paths ../../`echo "$${dir}-$${archive_prefix}php-$${php_version}" | sed -e "s/layers\//layer-/g"`.zip . ; \
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
			echo "### Publishing $${dir} PHP$${php_version} $${cpu}"; \
			echo "###"; \
			privateImage="bref/$${dir}-$${docker_prefix}php-$${php_version}"; \
			publicImage=$${privateImage/layers\//extra-}; \
			echo "Image name: $$publicImage"; \
			echo ""; \
			echo "docker push $$publicImage:latest"; \
			docker tag $$privateImage:latest $$publicImage:latest; \
			docker push $$publicImage:latest; \
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

