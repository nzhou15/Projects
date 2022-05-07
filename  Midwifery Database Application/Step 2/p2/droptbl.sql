-- Include your drop table DDL statements in this file.
-- Make sure to terminate each statement with a semicolon (;)

-- LEAVE this statement on. It is required to connect to your database.
CONNECT TO cs421;

-- Remember to put the drop table ddls for the tables with foreign key references
--    ONLY AFTER the parent tables has already been dropped (reverse of the creation order).
DROP TABLE tests;
DROP TABLE technicians;
DROP TABLE babies;
DROP TABLE info_session_registration;
DROP TABLE info_sessions;
DROP TABLE notes;
DROP TABLE appointments;
DROP TABLE pregnancies;
DROP TABLE parents;
DROP TABLE fathers;
DROP TABLE mothers;
DROP TABLE midwives;
DROP TABLE birth_centers;
DROP TABLE clinics;
DROP TABLE health_institutions;
