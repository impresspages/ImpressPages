mysql -e 'create database ip_test;'
sed -e 's/\[\[\[\[database\]\]\]\]/ip_test/g' < ../install/Plugin/Install/sql/structure.sql | sed -e 's/`ip_cms_/`ip_/g' | mysql -D ip_test # replace ip_cms_ and [[[[database]]]]
sed -e 's/`ip_cms_/`ip_/g' < ../install/Plugin/Install/sql/data.sql | mysql -D ip_test # replace ip_cms_ to ip_
sed -e 's/`ip_cms_/`ip_/g' < Fixture/data.sql | mysql -D ip_test # replace ip_cms_ to ip_
echo "USE mysql;\nUPDATE user SET password=PASSWORD('travis') WHERE user='travis';\nFLUSH PRIVILEGES;\n" | mysql -u travis # ImpressPages does not allow empty db password
