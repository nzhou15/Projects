-- Include your INSERT SQL statements in this file.
-- Make sure to terminate each statement with a semicolon (;)

-- LEAVE this statement on. It is required to connect to your database.
CONNECT TO cs421;

-- Remember to put the INSERT statements for the tables with foreign key references
--    ONLY AFTER the parent tables!
INSERT INTO health_institutions(hname, email, phone, website, address, hi_id) VALUES
('Lac-Saint-Louis', 'LacSaintLouis@teleworm.us', '416-722-5701', 'DumpJokes.ca','4845 Bayfield St, Stouffville, ON L4A 1T2', 2001)
,('Credit Birth center', 'CreditBirthCenter@rhyta.com',	'905-815-0047',	'SaltLakeCityResales.ca', '4029 Silver Springs Blvd, Calgary, AB T3E 0K6', 2002)
,('Blossom Valley Clinic', 'BlossomValley@dayrep.com', '450-612-6782', 'FareReview.ca',	'1750 Royal Avenue, New Westminster, BC V3L 5H1', 2003)
,('Publishing Birth center', 'PunlishingBirthCenter@armyspy.com', '403-346-0974', 'InvitationTracker.ca', '2665 Goyeau Ave, Windsor, ON N9A 1H9', 2004)
,('Goddess Birth center', 'GoddessBIrthCenter@rhyta.com', '519-821-9741', 'MormonVoice.ca',	'4243 Sherbrooke Ouest, Montreal, QC H4A 1H3', 2005)
;

INSERT INTO clinics(hi_id) VALUES (2001), (2003);
INSERT INTO birth_centers(hi_id) VALUES (2002), (2004), (2005);

INSERT INTO midwives(name, email, phone, practitioner_id, hi_id) VALUES
('Dominga Thomas', 'DomingaJThomas@armyspy.com', '204-975-3312', 1001, 2001)
,('Kate Weiner', 'KateGWeiner@dayrep.com', '250-674-1510', 1002, 2002)
,('Pat Cola', 'PatPCola@teleworm.us', '613-478-3242', 1003, 2003)
,('Shemika Urbanski', 'ShemikaSUrbanski@dayrep.com',	'819-622-7549', 1004, 2003)
,('Betty Rea', 'BettyLRea@armyspy.com',	'519-990-0990',	1005, 2004)
,('Marion Girard', 'MarionGirard@dayrep.com', '250-361-2697', 1006, 2005)
,('Lisa Neal', 'LisaJNeal@jourrapide.com', '514-757-5465', 1007, 2001)
;

INSERT INTO mothers(mname, hcardid, dateofbirth, address, phone, profession, blood_type) VALUES
('Violet Turner', 4775, DATE '1985-10-19', '1245 St. John Street, Allan, SK S4P 3Y2', '306-257-4047', 'Boiler mechanic', 'A')
,('Candice Head', 8128,  DATE '1988-12-20', '716 rue Fournier, Mascouche, QC J7K 1T3', '450-966-8846', 'Mortgage banker', 'O')
, ('Maria Nelson', 7539, DATE '1995-07-30', '1202 Eagle Rd, Toronto, ON M8Z 4H4', '416-734-7131', 'Econometrician', 'B')
,('Tracy Herrera', 7530, DATE '1992-01-25', '1740 Roger Street, Port Alberni, BC V9Y 4M8', '250-266-7937', 'Heat treating equipment setter', 'O')
,('Victoria Gutierrez', 4643, DATE '1995-10-26', '1972 Ross Street, Perth, ON K7H 3C7', '613-464-9370', 'Civil service clerk', 'A')
,('Ruth Corwin', 1987, DATE '1995-04-27', '4375 Albert Street, Stratford, ON N5A 3K5', '519-274-6665', 'Secretarial assistant', 'O')
,('Kathrine Collins', 3998, DATE '1994-03-13', '2515 Yonge Street, Toronto, ON M4W 1J7', '416-301-0340', 'Contract skidder', 'B')
('Sona Beahm', 7610, DATE '1985-09-01', '3941 Jasper Avenue, Edmonton, AB T5J 3N2', '780-240-9975', 'Marine surveyor','A')
,('Brianna Lee', 8460, DATE '1998-04-09', '3239 Rue King, Sherbrooke, QC J1H 1R4', '819-572-5472', 'Sportscaster', 'B')
,('Joyce Whitlock', 5981, DATE '1994-02-14', '3214 Yonge Street, Toronto, ON M4W 1J7', '416-464-1081', 'Public address system announcer', 'B')
,('Dorothea Smiley', 9993, DATE '1990-11-22', '380 Promenade du Portage, Hull, QC S4P 3Y2', '819-776-8252', 'Personnel technician', 'O')
,('Beth Wilson', 2085, DATE '1996-08-15', '993 Brand Road, Saskatoon, SK S7K 1W8', '306-382-9493',	'Fiscal technician', 'O')
,('Pamela Ingram', 6041, DATE '1988-12-05', '193 Princess St, Kingston, ON K7L 1C2', '613-546-4544', 'Optical mechanic', 'A')
,('Elizabeth Weist', 8826, DATE	'1995-04-28', '12 184th Street, Edmonton, AB T5J 2R4', '780-554-7233', 'Measurer', 'B')
,('Sharron Brown', 1232, DATE '1987-08-11', '545 Manitoba Street, Bracebridge, ON P1L 2B7', '705-641-4012', 'Snowmobile mechanic', 'AB')
,('Rhonda Bark', 2201, DATE '1988-09-02', '2454 Saint-Denis Street, Montreal, QC H9R 3J4', '514-695-8176', 'Audio control engineer', 'A')
;

DROP INDEX index_phone;
CREATE UNIQUE INDEX index_phone ON mothers(phone) INCLUDE (address);

SELECT hcardid, mname FROM mothers
WHERE hcardid IN ( SELECT hcardid FROM parents P
                   WHERE parent_id IN ( SELECT DISTINCT parent_id FROM pregnancies
                                        WHERE pregnancy_num >= 2))
;

WITH Temp(pregnancy_num) AS (
    SELECT COUNT(*) as pregnancy_num FROM pregnancies
    WHERE parent_id IN ( SELECT parent_id FROM parents
                         WHERE hcardid = 8128 )
    HAVING COUNT(*) >= 2
)
SELECT hcardid, mname, pregnancy_num FROM mothers, Temp
WHERE hcardid = 8128
;

INSERT INTO fathers(father_id, fname, hcardid, dateofbirth, address, phone, profession, blood_type) VALUES
(3001, 'Derek Brooks', 6341, DATE '1990-12-15', '3702 Bloor Street, Killam, AB T0B 2L0', '780-385-5160', 'Metallurgical engineer', 'O')
,(3002, 'William Banister', 7037, DATE '1988-06-13', '716 rue Fournier, Mascouche, QC J7K 1T3', '416-867-0917', 'Ordnance handling expert', 'A')
,(3003, 'Rudy Loring', 7965, DATE '1994-05-22', '1202 Eagle Rd, Toronto, ON M8Z 4H4', '250-763-9413', 'Composing machine tender', 'A')
,(3005, 'Glenn T. Robinson', 4507, DATE '1987-01-04', '1245 St. John Street, Allan, SK S4P 3Y2', '416-445-0507', 'Professional sports scout', 'A')
;

INSERT INTO fathers(father_id, fname, dateofbirth, phone, profession) VALUES
(3004, 'Andrew S. Gunter', DATE '1982-09-12', '519-475-7059', 'General and operations manager')
,(3006, 'James Grimes', DATE '1993-06-26', '613-321-2141', 'Engineering geologist')
,(3007,	'Michael Dempsey', DATE	'1977-02-05', '250-547-3722', 'Chiropractic doctor')
,(3008, 'Edward Romine', DATE '1987-02-02',	'250-237-6668',	'Forensic nurse')
,(3009,	'Thomas Hess', DATE	'1993-03-27', '418-866-0431', 'Industrial photographer')
,(3010,	'David S. Hanley', DATE	'1994-05-05', '905-303-7934', 'Industrial millwright')
;

INSERT INTO parents(parent_id, hcardid, father_id) VALUES
(4001, 7530, 3001)
,(4002, 8128, 3002)
,(4003, 7539, 3003)
,(4004, 4643, 3004)
,(4005, 4643, 3005)
,(4006, 1987, 3006)
,(4008, 7610, 3007)
,(4009, 9993, 3008)
,(4010, 5981, 3009)
,(4014, 8826, 3010)
,(4015, 9993, 3010)
,(4018, 4643, 3006)
,(4019, 8460, 3007)
;

INSERT INTO parents(parent_id, hcardid) VALUES
(4007, 3998)
,(4011, 8460)
,(4012, 2085)
,(4013, 6041)
,(4016, 1232)
,(4017, 2201)
;

INSERT INTO pregnancies(pregnancy_num, parent_id, expdueym, lmpdued, usounddued, estdued, interested, homebirth,
                        primary_practitioner_id, backup_practitioner_id, hi_id) VALUES
(1, 4001, DATE '2022-05-01', DATE '2022-05-21', DATE '2022-05-11', DATE '2022-05-21', 'yes', 'yes', 1001, 1002, 2002)
,(1, 4003, DATE '2022-06-01', DATE '2022-05-05', DATE '2022-06-25', DATE '2022-07-25', 'yes', 'yes', 1003, 1004, 2005)
, (1, 4004, DATE '2021-07-01',DATE '2021-07-26', DATE '2021-07-16', DATE '2021-07-16', 'yes', 'yes', 1004, 1005, 2002)
,(1, 4005, DATE '2020-04-01', DATE '2020-04-25', DATE '2020-04-14', DATE '2020-04-25', 'yes', 'yes', 1005, 1006, 2005)
,(2, 4005, DATE '2022-03-01', DATE '2022-03-02', DATE '2022-03-05', DATE '2022-03-05', 'yes', 'yes', 1006, 1001, 2004)
,(1, 4006, DATE '2022-08-01', DATE '2022-08-02', DATE '2022-08-12',	DATE '2022-08-12', 'yes', 'yes', 1003, 1002, 2002)
,(1, 4007, DATE '2022-05-01', DATE '2022-05-16', DATE '2022-05-13', DATE '2022-05-16', 'yes', 'yes', 1003, 1001, 2004)
,(1, 4002, DATE '2020-05-01', DATE '2020-05-12', DATE '2022-05-07', DATE '2022-05-16', 'no', 'yes', 1005, 1004, 2004)
;

INSERT INTO pregnancies(pregnancy_num, parent_id, expdueym, interested, homebirth,
                        primary_practitioner_id, backup_practitioner_id, hi_id) VALUES
(2, 4002, DATE '2022-07-01', 'yes', 'yes', 1002, 1003, 2004)
;

INSERT INTO pregnancies(pregnancy_num, parent_id, expdueym, lmpdued, usounddued, estdued,
                        primary_practitioner_id, backup_practitioner_id, hi_id) VALUES
(1, 4008, DATE '2019-01-01', DATE '2019-01-23', DATE '2019-01-13', DATE '2019-01-23', 1001, 1002, 2002)
,(2, 4008, DATE '2020-04-01', DATE '2020-04-04', DATE '2020-04-18', DATE '2020-04-18', 1002, 1003, 2005)
,(3, 4008, DATE '2022-01-01', DATE '2022-01-25', DATE '2022-01-26', DATE '2022-01-25', 1004, 1003, 2005)
,(1, 4009, DATE '2022-01-01', DATE '2022-01-09', DATE '2022-01-14', DATE '2022-01-09', 1005, 1004, 2002)
,(1, 4010, DATE '2019-12-01', DATE '2019-12-29', DATE '2019-12-15', DATE '2019-12-26', 1003, 1006, 2004)
,(2, 4010, DATE '2021-09-01', DATE '2021-09-11', DATE '2021-09-15', DATE '2021-09-15', 1004, 1006, 2004)
,(1, 4011, DATE '2022-02-01', DATE '2022-02-24', DATE '2022-02-25', DATE '2022-02-25', 1004, 1005, 2005)
,(1, 4012, DATE '2021-11-01', DATE '2021-11-06', DATE '2021-11-11', DATE '2021-11-06', 1001, 1006, 2002)
,(1, 4013, DATE '2019-05-01', DATE '2019-06-02', DATE '2019-05-27', DATE '2019-05-27', 1001, 1003, 2005)
,(2, 4013, DATE '2020-04-01', DATE '2015-04-30', DATE '2015-04-28', DATE '2015-04-30', 1002, 1004, 2002)
,(1, 4014, DATE '2019-05-01', DATE '2019-05-06', DATE '2019-05-15', DATE '2019-05-15', 1003, 1005, 2004)
,(2, 4014, DATE '2020-11-01', DATE '2020-11-09', DATE '2020-11-15', DATE '2020-11-15', 1004, 1002, 2005)
,(3, 4014, DATE '2022-03-01', DATE '2022-03-21', DATE '2022-03-10', DATE '2022-03-21', 1006, 1005, 2004)
,(1, 4015, DATE '2019-06-01', DATE '2019-06-01', DATE '2019-06-15', DATE '2019-06-01', 1005, 1003, 2004)
,(2, 4015, DATE '2020-11-01', DATE '2020-10-31', DATE '2020-11-09', DATE '2020-10-31', 1005, 1002, 2005)
,(1, 4016, DATE '2019-01-01', DATE '2019-01-21', DATE '2019-01-22', DATE '2019-01-22', 1006, 1005, 2004)
,(2, 4016, DATE '2020-05-01', DATE '2020-05-06', DATE '2020-05-12', DATE '2020-05-12', 1005, 1003, 2004)
,(3, 4016, DATE '2022-01-01', DATE '2022-01-14', DATE '2022-01-12', DATE '2022-01-14', 1001, 1006, 2002)
,(1, 4017, DATE '2019-11-01', DATE '2017-11-11', DATE '2017-11-28', DATE '2017-11-28', 1005, 1004, 2005)
,(2, 4017, DATE '2020-08-01', DATE '2020-08-05', DATE '2020-08-14', DATE '2020-08-05', 1004, 1006, 2005)
,(1, 4018, DATE '2021-05-01', DATE '2021-05-22', DATE '2021-05-28', DATE '2021-05-28', 1001, 1003, 2005)
,(1, 4019, DATE '2021-11-01', DATE '2021-11-11', DATE '2021-11-26', DATE '2021-11-11', 1002, 1004, 2002)
;

INSERT INTO appointments(a_id, date, time, pregnancy_num, parent_id, practitioner_id, hi_id) VALUES
(5001, DATE '2022-03-25', TIME '13:10', 1, 4001, 1001, 2002)
,(5002, DATE '2022-02-05', TIME '10:10', 2, 4002, 1003, 2004)
,(5003, DATE '2022-01-06', TIME '11:25', 1, 4003, 1003, 2005)
,(5004, DATE '2020-03-22', TIME '15:45', 1, 4005, 1005, 2002)
,(5005, DATE '2022-03-23', TIME '14:00', 2, 4005, 1006, 2005)
,(5006, DATE '2022-02-05', TIME '13:30', 1, 4003, 1003, 2005)
,(5007, DATE '2022-03-25', TIME '11:55', 2, 4005, 1001, 2004)
,(5008, DATE '2022-01-16', TIME '09:30', 1,	4006, 1003,	2002)
,(5009, DATE '2022-01-16', TIME '16:25', 1,	4007, 1003,	2004)
;

WITH A(pregnancy_num, parent_id, time) AS
(
    SELECT pregnancy_num, parent_id, time FROM appointments
    WHERE practitioner_id = 1003
), B(pregnancy_num, parent_id, temp, time) AS
(
    SELECT A.pregnancy_num, A.parent_id, (CASE WHEN primary_practitioner_id = 1003 THEN 'P'
                            WHEN backup_practitioner_id = 1003 THEN 'B' END) as temp, time FROM pregnancies P, A
    WHERE A.pregnancy_num = P.pregnancy_num AND A.parent_id = P.parent_id
), C(time, temp, hcardid, pregnancy_num, parent_id) AS
(
    SELECT time, temp, hcardid, B.pregnancy_num, B.parent_id FROM B, parents P
    WHERE B.parent_id = P.parent_id
)
SELECT time, temp, mname, C.hcardid, pregnancy_num, parent_id FROM C, mothers
WHERE mothers.hcardid = C.hcardid
ORDER BY time
;

INSERT INTO notes(n_id, date, time, content, pregnancy_num, parent_id) VALUES
(10001, DATE '2022-03-25', TIME '13:13', 'Prescribed a blood iron test', 1, 4001)
,(10002, DATE '2020-02-01', TIME '10:16', 'Prescribed a NIPT test', 1, 4002)
,(10003, DATE '2022-01-06', TIME '11:34', 'Decided final estimated date', 1, 4003)
,(10004, DATE '2020-03-22', TIME '15:46', 'Prescribed a blood iron test', 1, 4005)
,(10005, DATE '2022-03-23', TIME '14:25', 'First meeting', 2, 4005)
,(10006, DATE '2022-01-30', TIME '10:35', 'Couple would prefer home birth', 2, 4002)
,(10007, DATE '2021-12-05', TIME '15:55', 'Prescribed a blood iron test', 2, 4002)
;

SELECT date, time, LEFT(content, 50) as content, pregnancy_num, fname as couple_info FROM notes, fathers
WHERE parent_id IN ( SELECT parent_id FROM parents
                     WHERE hcardid = 8128 )
AND father_id IN ( SELECT father_id FROM parents
                   WHERE hcardid = 8128 )
;

INSERT INTO info_sessions(is_id, date, time, language, practitioner_id) VALUES
(6001, DATE '2021-12-20', TIME '14:00', 'English', 1001)
,(6002, DATE '2022-01-10', TIME '15:25', 'English', 1002)
,(6003, DATE '2022-01-10', TIME '10:05', 'French', 1003)
,(6004, DATE '2022-02-02', TIME '11:10', 'English', 1001)
,(6005, DATE '2021-12-29', TIME '14:00', 'French', 1004)
;

INSERT INTO info_session_registration(is_id, parent_id, attendance) VALUES
(6001, 4001, 'yes')
,(6002, 4002, 'no')
,(6003, 4003, 'yes')
,(6004, 4004, 'yes')
,(6005, 4005, 'no')
;

INSERT INTO babies(baby_id, dateofbirth, timeofbirth, gender, blood_type,
                   bname, pregnancy_num, parent_id) VALUES
(7005, DATE '2020-04-25', TIME '15:34', 'female', 'A', 'Norma Robinson', 1, 4005)
,(7006, DATE '2021-07-06', TIME '23:23', 'female', 'B', 'Cheryl Gutter', 1, 4004)
,(7007, DATE '2022-02-28', TIME '03:12', 'male', 'O', 'Danny Robinson', 2, 4005)
,(7008, DATE '2019-01-25', TIME '12:02', 'male', 'B', 'James Dempsey', 1, 4008)
,(7009, DATE '2020-04-02', TIME '22:04', 'male', 'O', 'Michael Dempsey', 2, 4008)
,(7010, DATE '2022-01-01', TIME '05:05', 'male', 'O', 'William Dempsey', 3, 4008)
,(7011, DATE '2022-01-14', TIME '10:12', 'female', 'B', 'Tiffany Romine', 1, 4009)
,(7012, DATE '2022-01-14', TIME '10:33', 'male', 'B', 'Adam Romine', 1, 4009)
,(7013, DATE '2019-12-04', TIME '11:02', 'male', 'A', 'Thomas Hess', 1, 4010)
,(7014, DATE '2022-02-10', TIME '14:29', 'female', 'B', 'Patricia Lee', 1, 4011)
,(7015, DATE '2022-02-10', TIME '15:01', 'female', 'B', 'Stormy Lee', 1, 4011)
,(7016, DATE '2022-11-05', TIME '12:12', 'female', 'A', 'Jennifer Wilson', 1, 4012)
,(7017, DATE '2019-05-23', TIME '20:12', 'female', 'A', 'Leah Ingram', 1, 4013)
,(7018, DATE '2020-04-18', TIME '21:45', 'female', 'O', 'Carrie Ingram', 2, 4013)
,(7019, DATE '2021-09-02', TIME '14:15', 'female', 'O', 'Valerie Hess', 2, 4010)
,(7020, DATE '2019-05-11', TIME '12:38', 'female', 'A', 'Alice Hanley', 1, 4014)
,(7021, DATE '2020-11-01', TIME '09:02', 'male', 'B', 'Kevin Hanley', 2, 4014)
,(7023, DATE '2022-03-01', TIME '18:29', 'male', 'A', 'Neal Hanley', 3, 4014)
,(7024, DATE '2019-05-31', TIME '17:18', 'female', 'B', 'Malanie Hanley', 1, 4015)
,(7022, DATE '2020-11-01', TIME '09:21', 'male', 'O', 'Stan Hanley', 2, 4015)
,(7025, DATE '2019-01-21', TIME '05:08', 'male', 'A', 'John Walsh', 1, 4016)
,(7026, DATE '2020-05-06', TIME '09:10', 'male', 'A', 'Jerome Stevens', 2, 4016)
,(7027, DATE '2022-01-14', TIME '10:22', 'male', 'O', 'William McKinnie', 3, 4016)
,(7028, DATE '2019-11-11', TIME '15:36', 'female', 'O', 'Leone Criswell', 1, 4017)
,(7029, DATE '2020-08-05', TIME '14:20', 'female', 'B', 'Lindsay Hewitt', 2, 4017)
,(7030, DATE '2020-08-05', TIME '23:15', 'female', 'B', 'Sonia Stroud', 2, 4017)
,(7031, DATE '2021-05-22', TIME '01:04', 'male', 'B', 'Robert McDowell', 1, 4018)
,(7032, DATE '2021-11-11', TIME '11:51', 'female', 'B', 'Mary Harvey', 1, 4019)
;

WITH A(baby_id, month) AS
(
    SELECT baby_id, MONTH(dateofbirth) as month FROM babies
    WHERE dateofbirth IS NOT NULL AND timeofbirth IS NOT NULL
    AND dateofbirth BETWEEN '2019-01-01' AND '2021-12-31'
)
SELECT month, COUNT(*) as count FROM A
GROUP BY month
ORDER BY month
;


INSERT INTO babies(baby_id, gender, blood_type, pregnancy_num, parent_id) VALUES
(7001, 'male', 'O', 1, 4001)
;

INSERT INTO babies(baby_id, gender, blood_type, bname, pregnancy_num, parent_id) VALUES
(7002, 'male', 'O', 'Edward Banister', 1, 4002)
,(7003, 'female', 'O', 'Elva Banister', 1, 4002)
,(7004, 'female', 'B', 'Brent Loring', 1, 4003)
;

INSERT INTO technicians(tech_id, tname, phone) VALUES
(8001, 'Sophia Garza', '519-741-6599')
,(8002, 'Lori Pittman', '250-481-0510')
,(8003, 'Dawn Frazio', '905-379-5120')
,(8004, 'Michael Medina', '905-214-4783')
,(8005, 'Garrett Estepp', '905-642-9937')
;

INSERT INTO tests(test_id, type, prescribed_date, sample_date, lab_date,
                  result, pregnancy_num, parent_id, practitioner_id, baby_id, tech_id) VALUES
(9002, 'NIPT', DATE '2020-03-06', DATE '2022-03-08', DATE '2022-03-08', 'Baby is male', 1, 4002, 1005, 7004, 8002)
;

INSERT INTO tests(test_id, type, prescribed_date, sample_date, lab_date,
                  result, pregnancy_num, parent_id, practitioner_id, tech_id) VALUES
(9001, 'blood iron', DATE '2021-11-23', DATE '2021-11-27', DATE '2021-11-30', 'Blood type is O-', 1, 4001, 1001, 8001)
, (9003, 'blood iron', DATE '2022-12-12', DATE '2022-12-16', DATE '2022-12-18', 'Blood type is A+', 1, 4003, 1003, 8003)
,(9004, 'dating ultrasound', DATE '2020-01-21', DATE '2020-01-29', DATE '2020-02-01', 'Expected due date: 2020-04-14', 1, 4005, 1005, 8004)
,(9005, 'blood iron', DATE '2021-10-14', DATE '2021-10-28', DATE '2021-10-28', 'Blood type is B+', 2, 4005, 1006, 8005)
,(9006, 'first trimmer ultrasound', DATE '2022-01-05', DATE '2022-01-05', DATE '2022-01-06', 'Fetal movement alright', 2, 4002, 1003, 8002)
,(9007, 'chorionic villus sampling', DATE '2021-11-26', DATE '2021-11-27', DATE '2021-11-29', 'No genetic problems found', 2, 4002, 1003, 8005)
;

SELECT prescribed_date, type, LEFT(result, 50) as result, pregnancy_num, fname as couple_info FROM tests, fathers
WHERE parent_id IN ( SELECT parent_id FROM parents
                     WHERE hcardid = 7539 )
AND father_id IN ( SELECT father_id FROM parents
                     WHERE hcardid = 7539 )
AND baby_id IS NULL
;

SELECT prescribed_date, type, result FROM tests
WHERE pregnancy_num = 1 AND parent_id = 4002 AND baby_id IS NULL
ORDER BY prescribed_date DESC
;

