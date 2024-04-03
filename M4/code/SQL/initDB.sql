SET SQLBLANKLINES ON;

BEGIN
   FOR cur_rec IN (SELECT object_name, object_type
                     FROM user_objects
                    WHERE object_type IN
                             ('TABLE',
                              'VIEW',
                              'PACKAGE',
                              'PROCEDURE',
                              'FUNCTION',
                              'SEQUENCE',
                              'SYNONYM',
                              'PACKAGE BODY'
                             ))
   LOOP
      BEGIN
         IF cur_rec.object_type = 'TABLE'
         THEN
            EXECUTE IMMEDIATE    'DROP '
                              || cur_rec.object_type
                              || ' "'
                              || cur_rec.object_name
                              || '" CASCADE CONSTRAINTS';
         ELSE
            EXECUTE IMMEDIATE    'DROP '
                              || cur_rec.object_type
                              || ' "'
                              || cur_rec.object_name
                              || '"';
         END IF;
      EXCEPTION
         WHEN OTHERS
         THEN
            DBMS_OUTPUT.put_line (   'FAILED: DROP '
                                  || cur_rec.object_type
                                  || ' "'
                                  || cur_rec.object_name
                                  || '"'
                                 );
      END;
   END LOOP;
   FOR cur_rec IN (SELECT * 
                   FROM all_synonyms 
                   WHERE table_owner IN (SELECT USER FROM dual))
   LOOP
      BEGIN
         EXECUTE IMMEDIATE 'DROP PUBLIC SYNONYM ' || cur_rec.synonym_name;
      END;
   END LOOP;
END;

/

-- Init Tables
CREATE TABLE User_table (
    UserID INTEGER PRIMARY KEY,
    Age INTEGER,
    Gender CHAR(1),
    Email CHAR(50) UNIQUE,
    UserWeight INTEGER
);

CREATE TABLE Device (
    DeviceID INTEGER PRIMARY KEY,
    Model CHAR(20)
);

CREATE TABLE Sleep (
    SleepID INTEGER PRIMARY KEY,
    sleepDate DATE UNIQUE,
    Duration INTEGER,
    Bedtime INTEGER
);

CREATE TABLE Recovery (
    RecoveryID INTEGER PRIMARY KEY,
    RecoveryScore INTEGER,
    recoveryDate DATE UNIQUE
);

CREATE TABLE Goals (
    GoalsID INTEGER PRIMARY KEY,
    startDate DATE,
    endDate DATE,
    goalDescription VARCHAR(100)
);

CREATE TABLE WeightLoss (
    GoalsID INTEGER PRIMARY KEY,
    TargetLoss INTEGER,
    FOREIGN KEY (GoalsID) REFERENCES Goals(GoalsID) ON DELETE CASCADE
);

CREATE TABLE MuscleGain (
    GoalsID INTEGER PRIMARY KEY,
    TargetGain INTEGER,
    FOREIGN KEY (GoalsID) REFERENCES Goals(GoalsID) ON DELETE CASCADE
);

CREATE TABLE Active (
    GoalsID INTEGER PRIMARY KEY,
    TargetActivity CHAR(20),
    FOREIGN KEY (GoalsID) REFERENCES Goals(GoalsID) ON DELETE CASCADE
);

CREATE TABLE NutritionInputs (
    NutritionID INTEGER PRIMARY KEY,
    DeviceID INTEGER,
    UserID INTEGER,
    Calories INTEGER,
    NutritionInputsDate DATE,
    FOREIGN KEY (DeviceID) REFERENCES Device(DeviceID),
    FOREIGN KEY (UserID) REFERENCES User_table(UserID)
);

CREATE SEQUENCE NutritionID_seq START WITH 6 INCREMENT BY 1;

CREATE TABLE GenerateData (
    DataID INTEGER PRIMARY KEY,
    GoalsID INTEGER,
    RecoveryID INTEGER,
    SleepID INTEGER,
    DeviceID INTEGER,
    Score INTEGER,
    GenerateDataDate DATE, 
    FOREIGN KEY (SleepID) REFERENCES Sleep(SleepID), --ON DELETE SET NULL ON UPDATE CASCADE
    FOREIGN KEY (RecoveryID) REFERENCES Recovery(RecoveryID),
    FOREIGN KEY (GoalsID) REFERENCES Goals(GoalsID),
    FOREIGN KEY (DeviceID) REFERENCES Device(DeviceID)
);

CREATE TABLE InsightMonitors (
    InsightID INTEGER,
    Result VARCHAR(255),
    InsightMonitorsDate DATE,
    UserID INTEGER,
    PRIMARY KEY (InsightID, UserID),
    FOREIGN KEY (UserID) REFERENCES User_table(UserID) ON DELETE CASCADE
);

CREATE TABLE InsightProvides (
    InsightID INTEGER PRIMARY KEY,
    Result VARCHAR(255),
    InsightProvidesDate DATE,
    DeviceID INTEGER,
    FOREIGN KEY (DeviceID) REFERENCES Device(DeviceID) ON DELETE CASCADE --ON DELETE SET NULL ON UPDATE CASCADE
);


-- POPULATE

INSERT ALL
INTO User_table VALUES (1, 25, 'M', 'omar@gmail.com', 75)
INTO User_table VALUES (2, 30, 'F', 'julie@gmail.com', 59)
INTO User_table VALUES (3, 40, 'M', 'seif@gmail.com', 86)
INTO User_table VALUES (4, 35, 'F', 'sara@gmail.com', 62)
INTO User_table VALUES (5, 28, 'M', 'zaid@gmail.com', 68)

INTO Device VALUES (1, 'MyFitTrackerV2')
INTO Device VALUES (2, 'MyFitTrackerV2')
INTO Device VALUES (3, 'MyFitTrackerV2Pro')
INTO Device VALUES (4, 'MyFitTrackerV1')
INTO Device VALUES (5, 'MyFitTrackerV4')

INTO Sleep VALUES (1, '01-FEB-2024', 420, 23)
INTO Sleep VALUES (2, '02-FEB-2024', 390, 9)
INTO Sleep VALUES (3, '03-FEB-2024', 450, 22)
INTO Sleep VALUES (4, '04-FEB-2024', 480, 11)
INTO Sleep VALUES (5, '05-FEB-2024', 400, 10)

INTO Recovery VALUES (1, 80, '01-FEB-2024')
INTO Recovery VALUES (2, 75, '02-FEB-2024')
INTO Recovery VALUES (3, 85, '03-FEB-2024')
INTO Recovery VALUES (4, 90, '04-FEB-2024')
INTO Recovery VALUES (5, 82, '05-FEB-2024')

INTO Goals VALUES (1, '01-JAN-2024', '30-JUN-2024', 'Lose weight')
INTO Goals VALUES (2, '15-JAN-2024', '15-JUL-2024', 'Gain muscle')
INTO Goals VALUES (3, '01-FEB-2024', '30-APR-2024', 'Increase recovery time')
INTO Goals VALUES (4, '01-MAR-2024', '30-SEP-2024', 'Eat healthier')
INTO Goals VALUES (5, '15-FEB-2024', '15-AUG-2024', 'Stick to a bedtime')

INTO WeightLoss VALUES (1, 10)
INTO WeightLoss VALUES (4, 5)
INTO WeightLoss VALUES (5, 3)
INTO WeightLoss VALUES (2, 0)
INTO WeightLoss VALUES (3, 2)

INTO MuscleGain VALUES (2, 5)
INTO MuscleGain VALUES (1, 2)
INTO MuscleGain VALUES (3, 10)
INTO MuscleGain VALUES (4, 3)
INTO MuscleGain VALUES (5, 7)

INTO Active VALUES (3, 'Soccer')
INTO Active VALUES (5, 'Boxing')
INTO Active VALUES (1, 'Calisthenics')
INTO Active VALUES (2, 'Basketball')
INTO Active VALUES (4, 'Swimming')

INTO NutritionInputs VALUES (1, 1, 1, 2000, '01-FEB-2024')
INTO NutritionInputs VALUES (2, 2, 2, 1800, '02-FEB-2024')
INTO NutritionInputs VALUES (3, 3, 3, 2200, '03-FEB-2024')
INTO NutritionInputs VALUES (4, 4, 4, 1900, '04-FEB-2024')
INTO NutritionInputs VALUES (5, 5, 5, 2100, '05-FEB-2024')

INTO GenerateData VALUES (1, 1, 1, 1, 1, 99, '01-MAR-2020')
INTO GenerateData VALUES (2, 2, 2, 2, 1, 80, '02-MAR-2020')
INTO GenerateData VALUES (3, 3, 3, 3, 3, 99, '03-MAR-2020')
INTO GenerateData VALUES (4, 4, 4, 4, 1, 99, '04-MAR-2020')
INTO GenerateData VALUES (5, 5, 5, 5, 1, 99, '05-MAR-2020')

INTO InsightMonitors VALUES (1, 'You slept well last night', '01-FEB-2024', 1)
INTO InsightMonitors VALUES (2, 'Your recovery score is improving!', '02-FEB-2024', 2)
INTO InsightMonitors VALUES (3, 'Keep up the good work on your goals!', '03-FEB-2024', 3)
INTO InsightMonitors VALUES (4, 'Consider adjusting your nutrition for better results', '04-FEB-2024', 4)
INTO InsightMonitors VALUES (5, 'Remember to stretch before exercising', '05-FEB-2024', 5)

INTO InsightProvides VALUES (1, 'Recovery decreased', '01-FEB-2024', 1)
INTO InsightProvides VALUES (2, 'Sleep duration got better', '02-FEB-2024', 2)
INTO InsightProvides VALUES (3, 'Bedtime becomes earlier', '03-FEB-2024', 3)
INTO InsightProvides VALUES (4, 'Nutrition needs more calories', '04-FEB-2024', 4)
INTO InsightProvides VALUES (5, 'Activity updated', '05-FEB-2024', 5)

SELECT 1 FROM DUAL;

