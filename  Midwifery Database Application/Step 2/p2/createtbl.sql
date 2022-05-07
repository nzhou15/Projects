-- Include your create table DDL statements in this file.
-- Make sure to terminate each statement with a semicolon (;)

-- LEAVE this statement on. It is required to connect to your database.
CONNECT TO cs421;

-- Remember to put the create table ddls for the tables with foreign key references
-- ONLY AFTER the parent tables has already been created.
CREATE TABLE health_institutions
(
    hname      VARCHAR(30)      NOT NULL
    ,email     VARCHAR(75)      NOT NULL UNIQUE
    ,phone     VARCHAR(15)      NOT NULL
    ,website   VARCHAR(30)      NOT NULL
    ,address   VARCHAR(100)     NOT NULL
    ,hi_id     INTEGER          NOT NULL
    ,PRIMARY KEY(hi_id)
);

CREATE TABLE clinics
(
    hi_id       INTEGER     NOT NULL
    ,FOREIGN KEY(hi_id) REFERENCES health_institutions(hi_id)
    ,PRIMARY KEY(hi_id)
);

CREATE TABLE birth_centers
(
    hi_id       INTEGER     NOT NULL
    ,FOREIGN KEY(hi_id) REFERENCES health_institutions(hi_id)
    ,PRIMARY KEY(hi_id)
);

CREATE TABLE midwives
(
    name                VARCHAR(30)     NOT NULL
    ,email              VARCHAR(75)     NOT NULL UNIQUE
    ,phone              VARCHAR(15)     NOT NULL
    ,practitioner_id    INTEGER         NOT NULL
    ,hi_id              INTEGER         NOT NULL
    ,FOREIGN KEY(hi_id) REFERENCES health_institutions(hi_id)
    ,PRIMARY KEY(practitioner_id)
);

DROP TABLE mothers;
CREATE TABLE mothers
(
    mname          VARCHAR(30)     NOT NULL
    ,hcardid       INTEGER         NOT NULL
    ,dateofbirth   DATE            NOT NULL
    ,address       VARCHAR(100)    NOT NULL
    ,phone         VARCHAR(15)     NOT NULL
    ,profession    VARCHAR(50)     NOT NULL
    ,blood_type    VARCHAR(5)
    ,PRIMARY KEY(hcardid)
);

DROP TABLE fathers;
CREATE TABLE fathers
(
    father_id      INTEGER         NOT NULL
    ,fname         VARCHAR(30)     NOT NULL
    ,hcardid       INTEGER
    ,dateofbirth   DATE            NOT NULL
    ,address       VARCHAR(100)
    ,phone         VARCHAR(15)     NOT NULL
    ,profession    VARCHAR(50)     NOT NULL
    ,blood_type    VARCHAR(5)
    ,PRIMARY KEY(father_id)
);

DROP TABLE parents;
CREATE TABLE parents
(
    parent_id      INTEGER      NOT NULL
    ,hcardid       INTEGER      NOT NULL
    ,father_id     INTEGER      
    ,FOREIGN KEY(hcardid) REFERENCES mothers(hcardid)
    ,FOREIGN KEY(father_id) REFERENCES fathers(father_id)
    ,PRIMARY KEY(parent_id)
);

DROP TABLE pregnancies;
CREATE TABLE pregnancies
(
    pregnancy_num               INTEGER     NOT NULL
    ,parent_id                  INTEGER     NOT NULL
    ,expdueym                   DATE        NOT NULL
    ,lmpdued                    DATE
    ,usounddued                 DATE
    ,estdued                    DATE
    ,interested                 VARCHAR(10)
    ,homebirth                  VARCHAR(10)
    ,primary_practitioner_id    INTEGER     NOT NULL
    ,backup_practitioner_id     INTEGER     NOT NULL
    ,hi_id                      INTEGER     NOT NULL
    ,FOREIGN KEY(parent_id) REFERENCES parents(parent_id)
    ,FOREIGN KEY(primary_practitioner_id) REFERENCES midwives(practitioner_id)
    ,FOREIGN KEY(backup_practitioner_id) REFERENCES midwives(practitioner_id)
    ,FOREIGN KEY(hi_id) REFERENCES birth_centers(hi_id)
    ,PRIMARY KEY(pregnancy_num, parent_id)
);

DROP TABLE appointments;
CREATE TABLE appointments
(
    a_id                INTEGER     NOT NULL
    ,date               DATE        NOT NULL
    ,time               TIME        NOT NULL
    ,pregnancy_num      INTEGER     NOT NULL
    ,parent_id          INTEGER     NOT NULL
    ,practitioner_id    INTEGER     NOT NULL
    ,hi_id              INTEGER     NOT NULL
    ,FOREIGN KEY(pregnancy_num, parent_id) REFERENCES pregnancies(pregnancy_num, parent_id)
    ,FOREIGN KEY(practitioner_id) REFERENCES midwives(practitioner_id)
    ,FOREIGN KEY(hi_id) REFERENCES birth_centers(hi_id)
    ,PRIMARY KEY(a_id)
);

DROP TABLE notes;
CREATE TABLE notes
(
    n_id            INTEGER                 NOT NULL
    ,date           DATE                    NOT NULL
    ,time           TIME                    NOT NULL
    ,content        VARCHAR(200)            NOT NULL
    ,pregnancy_num  INTEGER                 NOT NULL
    ,parent_id      INTEGER                 NOT NULL
    ,FOREIGN KEY(pregnancy_num, parent_id) REFERENCES pregnancies(pregnancy_num, parent_id)
    ,PRIMARY KEY(n_id)
);

CREATE TABLE info_sessions
(
    is_id               INTEGER         NOT NULL
    ,date               DATE            NOT NULL
    ,time               TIME            NOT NULL
    ,language           VARCHAR(20)     NOT NULL
    ,practitioner_id    INTEGER         NOT NULL
    ,FOREIGN KEY(practitioner_id) REFERENCES midwives(practitioner_id)
    ,PRIMARY KEY(is_id)
);

CREATE TABLE info_session_registration
(
    is_id           INTEGER         NOT NULL
    ,parent_id      INTEGER         NOT NULL
    ,attendance     VARCHAR(10)     NOT NULL
    ,PRIMARY KEY(is_id)
);

DROP TABLE babies;
CREATE TABLE babies
(
    baby_id         INTEGER         NOT NULL
    ,dateofbirth    DATE
    ,timeofbirth    TIME
    ,gender         VARCHAR(10)
    ,blood_type     VARCHAR(5)
    ,bname           VARCHAR(30)
    ,pregnancy_num  INTEGER         NOT NULL
    ,parent_id      INTEGER         NOT NULL
    ,FOREIGN KEY(pregnancy_num, parent_id) REFERENCES pregnancies(pregnancy_num, parent_id)
    ,PRIMARY KEY(baby_id)
);

CREATE TABLE technicians
(
    tech_id        INTEGER         NOT NULL
    ,tname      VARCHAR(30)     NOT NULL
    ,phone      VARCHAR(15)     NOT NULL
    ,PRIMARY KEY(tech_id)
);

DROP TABLE tests;
CREATE TABLE tests
(
    test_id             INTEGER         NOT NULL
    ,type               VARCHAR(50)     NOT NULL
    ,prescribed_date    DATE
    ,sample_date        DATE
    ,lab_date           DATE
    ,result             VARCHAR(200)
    ,pregnancy_num      INTEGER         NOT NULL
    ,parent_id          INTEGER         NOT NULL
    ,practitioner_id    INTEGER         NOT NULL
    ,baby_id            INTEGER
    ,tech_id            INTEGER
    ,FOREIGN KEY(pregnancy_num, parent_id) REFERENCES pregnancies(pregnancy_num, parent_id)
    ,FOREIGN KEY(practitioner_id) REFERENCES midwives(practitioner_id)
    ,FOREIGN KEY(baby_id) REFERENCES babies(baby_id)
    ,FOREIGN KEY(tech_id) REFERENCES technicians(tech_id)
    ,PRIMARY KEY(test_id)
);