SET SQLBLANKLINES ON;

-- Clear DB
BEGIN
DROP TABLE IF EXISTS GenerateRecoveryData;
DROP TABLE IF EXISTS GenerateGoalsData;
DROP TABLE IF EXISTS GenerateSleepData;
DROP TABLE IF EXISTS DeviceTracksData;
DROP TABLE IF EXISTS InsightMonitors;
DROP TABLE IF EXISTS InsightProvides;
DROP TABLE IF EXISTS NutritionInputs;
DROP TABLE IF EXISTS Active;
DROP TABLE IF EXISTS MuscleGain;
DROP TABLE IF EXISTS WeightLoss;
DROP TABLE IF EXISTS Goals;
DROP TABLE IF EXISTS Recovery;
DROP TABLE IF EXISTS Sleep;
DROP TABLE IF EXISTS Device;
DROP TABLE IF EXISTS Users;
END;
/

-- Init Tables
CREATE TABLE User (
    UserID INTEGER PRIMARY KEY,
    Age INTEGER,
    Gender CHAR(1)
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
    FOREIGN KEY (GoalsID)
        REFERENCES Goals(GoalsID)
);

CREATE TABLE MuscleGain (
    GoalsID INTEGER PRIMARY KEY,
    TargetGain INTEGER,
    FOREIGN KEY (GoalsID)
        REFERENCES Goals(GoalsID)
);

CREATE TABLE Active (
    GoalsID INTEGER PRIMARY KEY,
    TargetActivity CHAR(20),
    FOREIGN KEY (GoalsID)
        REFERENCES Goals(GoalsID)
);

CREATE TABLE NutritionInputs (
    NutritionID INTEGER PRIMARY KEY,
    DeviceID INTEGER,
    UserID INTEGER,
    Calories INTEGER,
    NutritionInputsDate DATE,
    FOREIGN KEY (DeviceID)
        REFERENCES Device(DeviceID),
    FOREIGN KEY (UserID)
        REFERENCES User(UserID)
);

CREATE TABLE GenerateRecoveryData (
    DataID INTEGER PRIMARY KEY,
    GenerateRecoveryDate DATE,
    GenerateRecoveryValue INTEGER,
    RecoveryID INTEGER,
    FOREIGN KEY (RecoveryID)
        REFERENCES Recovery(RecoveryID)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE GenerateGoalsData (
    DataID INTEGER PRIMARY KEY,
    GenerateGoalsDate DATE,
    GenerateGoalsValue INTEGER,
    GoalID INTEGER,
    DeviceID INTEGER,
    FOREIGN KEY (GoalID) REFERENCES Goals(GoalsID),
        ON DELETE SET NULL
        ON UPDATE CASCADE );

CREATE TABLE GenerateSleepData (
    DataID INTEGER PRIMARY KEY,
    GenerateSleepDate DATE,
    GenerateSleepValue INTEGER,
    SleepID INTEGER,
    FOREIGN KEY (SleepID)
        REFERENCES Sleep(SleepID)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE DeviceTracksData (
    DataID INTEGER PRIMARY KEY,
    DeviceTracksDataDate DATE,
    DeviceTracksDataValue INTEGER,
    DeviceID INTEGER,
    FOREIGN KEY (DeviceID)
        REFERENCES Device(DeviceID)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE InsightMonitors (
    InsightID INTEGER,
    Result VARCHAR,
    InsightMonitorsDate DATE,
    UserID INTEGER,
    PRIMARY KEY (InsightID, UserID),
    FOREIGN KEY (UserID)
        REFERENCES User(UserID)
        ON DELETE CASCADE
);

CREATE TABLE InsightProvides (
    InsightID INTEGER PRIMARY KEY,
    Result VARCHAR,
    InsightProvidesDate DATE,
    DeviceID INTEGER,
    FOREIGN KEY (DeviceID)
        REFERENCES Device(DeviceID)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- POPULATE

INSERT INTO User(UserID, Age, Gender)
VALUES
(1, 25, 'M'),
(2, 30, 'F'),
(3, 40, 'M'),
(4, 35, 'F'),
(5, 28, 'M');

INSERT INTO Device (DeviceID, Model)
VALUES
(1, 'MyFitTrackerV2'),
(2, 'MyFitTrackerV2'),
(3, 'MyFitTrackerV2Pro'),
(4, 'MyFitTrackerV1'),
(5, 'MyFitTrackerV4');

INSERT INTO Sleep (SleepID, sleepDate, Duration, Bedtime)
VALUES
(1, '2024-02-01', 420, 23),
(2, '2024-02-02', 390, 9),
(3, '2024-02-03', 450, 22),
(4, '2024-02-04', 480, 11),
(5, '2024-02-05', 400, 10);

INSERT INTO Recovery (RecoveryID, RecoveryScore, recoveryDate)
VALUES (1, 80, '2024-02-01'),
(2, 75, '2024-02-02'),
(3, 85, '2024-02-03'),
(4, 90, '2024-02-04'),
(5, 82, '2024-02-05');

INSERT INTO Goals (GoalsID, startDate, End_Date, goalDescription)
VALUES
(1, '2024-01-01', '2024-06-30', 'Lose weight'),
(2, '2024-01-15', '2024-07-15', 'Gain muscle'),
(3, '2024-02-01', '2024-04-30', 'Increase recovery time'),
(4, '2024-03-01', '2024-09-30', 'Eat healthier'),
(5, '2024-02-15', '2024-08-15', 'Stick to a bedtime');

INSERT INTO WeightLoss (GoalsID, TargetLoss)
VALUES
(1, 10),
(4, 5),
(5, 3),
(2, 0),
(3, 2);

INSERT INTO MuscleGain (GoalsID, TargetGain)
VALUES
(2, 5),
(1, 2),
(3, 10),
(4, 3),
(5, 7);

INSERT INTO Active (GoalsID, TargetActivity)
VALUES
(3, 'Soccer'),
(5, 'Boxing'),
(1, 'Calisthenics'),
(2, 'Basketball'),
(4, 'Swimming');

INSERT INTO NutritionInputs (NutritionID, DeviceID, UserID, Calories, NutritionInputsDate)
VALUES
(1, 1, 1, 2000, '2024-02-01'),
(2, 2, 2, 1800, '2024-02-02'),
(3, 3, 3, 2200, '2024-02-03'),
(4, 4, 4, 1900, '2024-02-04'),
(5, 5, 5, 2100, '2024-02-05');

INSERT INTO GenerateSleepData (DataID, GenerateSleepDate, GenerateSleepValue, SleepID)
VALUES
(1, '2024-03-01', 87, 1),
(2, '2024-03-02', 76, 1),
(3, '2024-03-03', 98, 2),
(4, '2024-03-04', 57, 2),
(5, '2024-03-05', 66, 3);

INSERT INTO GenerateRecoveryData (DataID, GenerateRecoveryDate, GenerateRecoveryValue, RecoveryID)
VALUES
(1, '2024-03-01', 73, 1),
(2, '2024-03-02', 64, 1),
(3, '2024-03-03', 95, 2),
(4, '2024-03-04', 63, 2),
(5, '2024-03-05', 74, 3);

INSERT INTO GenerateGoalsData (DataID, GenerateGoalsDate, GenerateGoalsValue, GoalID)
VALUES
(1, '2024-03-01', 97, 1),
(2, '2024-03-02', 54, 1),
(3, '2024-03-03', 98, 2),
(4, '2024-03-04', 95, 2),
(5, '2024-03-05', 60, 3);

INSERT INTO DeviceTracksData (DataID, DeviceTracksDataDate, DeviceTracksDataValue, DeviceID)
VALUES
(1, '2024-03-01', 97, 1),
(2, '2024-03-02', 54, 1),
(3, '2024-03-03', 98, 2),
(4, '2024-03-04', 95, 2),
(5, '2024-03-05', 60, 3);

INSERT INTO InsightMonitors (InsightID, Result, InsightMonitorsDate, UserID)
VALUES
(1, 'You slept well last night', '2024-02-01', 1),
(2, 'Your recovery score is improving!', '2024-02-02', 2),
(3, 'Keep up the good work on your goals!', '2024-02-03', 3),
(4, 'Consider adjusting your nutrition for better results', '2024-02-04', 4),
(5, 'Remember to stretch before exercising', '2024-02-05', 5);

INSERT INTO InsightProvides (InsightID, Result, InsightProvidesDate, DeviceID)
VALUES
(1, 'Recovery decreased', '2024-05-01', 1),
(2, 'Sleep duration got better', '2024-07-02', 2),
(3, 'Bedtime becomes earlier', '2024-02-03', 3),
(4, 'Nutrition needs more calories', '2024-02-04', 4),
(5, 'Activity updated', '2024-02-05', 5);