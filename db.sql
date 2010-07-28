CREATE TABLE IF NOT EXISTS search (
    sid INTEGER PRIMARY KEY,
    added DEFAULT CURRENT_TIMESTAMP,
    lastget DEFAULT CURRENT_TIMESTAP,
    region DEFAULT 'com',
    query
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

INSERT INTO search (query,region) VALUES ('Asus 1015','de');
