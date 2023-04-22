FROM prestashop/prestashop:1.7.8-7.1

RUN apt-get update && apt-get install -y \
  git \
  && rm -rf /var/lib/apt/lists/*
