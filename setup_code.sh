#!/usr/bin/env bash

real_path=$(cd $(dirname $0) && pwd)
cd ${real_path}

if [ ! -d "yaaw" ]; then
    git clone https://github.com/binux/yaaw.git
fi


if [ ! -d "xunlei-lixian" ]; then
    git clone https://github.com/iambus/xunlei-lixian.git
    ln -s ./xunlei-lixian/lixian_cli.py lx
fi


if [ ! -f "composer.phar" ]; then
    php -r "readfile('https://getcomposer.org/installer');" | php
fi

php composer.phar install

