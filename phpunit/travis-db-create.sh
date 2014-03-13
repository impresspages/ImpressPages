# Create test db
mysql -e 'create database ip_test;'
sed -e 's/\[\[\[\[database\]\]\]\]/ip_test/g' < ../install/Plugin/Install/sql/structure.sql | mysql -D ip_test # replace [[[[database]]]]
mysql -D ip_test < ../install/Plugin/Install/sql/data.sql
mysql -D ip_test < Fixture/data.sql

# Set travis user password
echo "USE mysql;\nUPDATE user SET password=PASSWORD('travis') WHERE user='travis';\nFLUSH PRIVILEGES;\n" | mysql -u travis # ImpressPages does not allow empty db password
