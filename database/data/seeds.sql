
# Attributes
SELECT `name`, `values`
FROM attributes
INTO OUTFILE '/tmp/attributes.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY ''
LINES TERMINATED BY '\n';
