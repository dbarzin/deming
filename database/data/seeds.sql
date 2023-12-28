
# Attributes
SELECT `name`, `values`
FROM attributes
INTO OUTFILE '/tmp/attributes.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY ''
LINES TERMINATED BY '\n';

# Domains
SELECT `id`, `title`, `description`
FROM domains
INTO OUTFILE '/tmp/domains.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY ''
LINES TERMINATED BY '\n';

# Measures
SELECT `domain_id`, `clause`, `name`,  `objective`, `attributes`, `input`, `model`, `indicator`, `action_plan`
FROM measures
INTO OUTFILE '/tmp/measures.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY ''
LINES TERMINATED BY '\n';
