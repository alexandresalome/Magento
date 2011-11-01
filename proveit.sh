#!/bin/bash

echo 'CREATE DATABASE magento' | mysql -uroot
chmod a+w app/etc var media

php -f install.php -- \
  --license_agreement_accepted yes   \
  --locale                     fr_FR \
  --timezone                   "Europe/Paris"   \
  --default_currency           EUR              \
  --db_host                   "localhost"       \
  --db_name                   "magento"         \
  --db_user                   "root"            \
  --db_pass                   ""                \
  --db_prefix                  magento_         \
  --use_rewrites               yes              \
  --url                        "http://magento.local" \
  --use_secure                 yes              \
  --secure_base_url            "http://magento.local" \
  --use_secure_admin           yes              \
  --admin_username             admin            \
  --admin_password             admin123         \
  --admin_firstname            "Administrateur" \
  --admin_lastname             "Administrateur" \
  --admin_email                "alexandre.salome@gmail.com" \
  --skip_url_validation
