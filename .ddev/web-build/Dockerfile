ARG BASE_IMAGE
FROM $BASE_IMAGE

# Downgrade libssl before install php7.3 and pecl.
RUN apt-get update #&& apt install --reinstall libssl1.1=1.1.1d-0+deb10u6 -y --allow-downgrades --fix-missing
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y -o Dpkg::Options::="--force-confold" --no-install-recommends --no-install-suggests make libssl-dev php-pear php7.3-dev
# The "echo" below just forces accepting the "automatic" configuration, the same as hitting <RETURN>
RUN echo | sudo pecl install jsmin
