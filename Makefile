.PHONY: prepare sync clean clean-export build publish publish-canary publish-experimental test lambda

prepare:
	git clone git@github.com:brefphp/bref.git build/bref

sync:
	git -C build/bref reset --hard HEAD
	git -C build/bref pull

clean:
	rm -rf build/bref

clean-export:
	rm -rf build/bref/runtime/export/tmp/**

build:
	# Just build from Docker hub
	sed -i '' -e 's/export\/php%.zip: docker-images/export\/php%.zip:/g' build/bref/runtime/Makefile

	# Create binaries
	cd build/bref/runtime && make export/php-80-fpm.zip

	# Ensure folder
	mkdir -p native

	# Cleanup folder
	rm -rf native/**

	# Extract binaries
	tar zxvf build/bref/runtime/export/php-80-fpm.zip -C native

	# Download composer
	curl -sS https://getcomposer.org/installer | php -- --install-dir=native/bin --filename=composer

	# Replace PHP paths
	#sed -i '' -e "s/\/opt\/bin\/php/\/var\/task\/native\/bref\/bin\/php/g" ./native/bootstrap

	# Remove brefphp's bootstrap
	rm ./native/bootstrap

	# Use our tuned brefphp bootstrap
	cp lib/brefphp ./native

	# Use our tuned PHP ini
	cp lib/vercel.ini ./native/bref/etc/php/conf.d/vercel.ini

	# Create zip
	zip --symlinks -r native.zip native

publish:
	rm -rf ./dist
	npm publish --access public --tag latest

publish-dry:
	rm -rf ./dist
	npm publish --dry

publish-canary:
	rm -rf ./dist
	npm version --no-git-tag-version patch
	npm publish --access public --tag canary

publish-experimental:
	rm -rf ./dist
	npm version --no-git-tag-version patch
	npm publish --access public --tag experimental

test:
	npm run build
	npm run test

lambda:
	docker run \
		-it \
		--rm \
		-v $(CURDIR)/native:/var/task/native \
		-v $(CURDIR)/lib/bootstrap:/var/task/bootstrap \
		-w /var/task \
		--entrypoint /bin/bash \
		-p 8000:8000 \
		lambci/lambda:nodejs12.x
