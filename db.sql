CREATE TABLE IF NOT EXISTS search (
    sid INTEGER PRIMARY KEY,
    dt DEFAULT CURRENT_TIMESTAMP,
    region DEFAULT 'com',
    query,
    email
);

CREATE TABLE IF NOT EXISTS search_results (
    sid INTEGER REFERENCES search(sid) ON DELETE CASCADE,
    asin,
    dt DEFAULT CURRENT_TIMESTAMP,
    title,
    more,
    price,
    url TEXT,
    image TEXT,

    PRIMARY KEY (sid, asin)
);

INSERT INTO search (query,email,region) VALUES ('Asus 1015','andi@splitbrain.org','de');
