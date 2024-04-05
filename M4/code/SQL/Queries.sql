-- INSERT
-- INSERT a new Sleep routine

INSERT INTO User
VALUES (
    :UserID,
    :Age,
    :Gender
);

INSERT INTO Device
VALUES (
    :DeviceID,
    :Model
);

INSERT INTO Sleep
VALUES (
    :SleepID,
    :sleepDate,
    :Duration,
    :Bedtime
);

INSERT INTO Recovery
VALUES (
    :RecoveryID,
    :RecoveryScore,
    :recoveryDate
);

INSERT INTO GOALS
VALUES (
    :GoalsID,
    :startDate;
    :endDate,
    :goalDescription
);

INSERT INTO WeightLoss
VALUES (
    :GoalsID,
    :TargetLoss
);

INSERT INTO MuscleGain
VALUES (
    :GoalsID,
    :TargetGain
);

INSERT INTO Active
VALUES (
    :GoalsID,
    :TargetActivity
);

INSERT INTO NutritionInputs
VALUES (
    :NutritionID,
    :DeviceID,
    :UserID,
    :Calories,
    :NutritionInputsDate
);

INSERT INTO GenerateRecoveryData
VALUES (
    :DataID,
    :GenerateRecoveryDate,
    :GenerateRecoveryValue,
    :RecoveryID
);

INSERT INTO GenerateGoalsData
VALUES (
    :DataID,
    :GenerateGoalsDate,
    :GenerateGoalsValue,
    :GoalsID,
    :DeviceID
);

INSERT INTO GenerateSleepDate
VALUES (
    :DataID,
    :GenerateSleepDate,
    :GenerateSleepValue,
    :SleepID
);

INSERT INTO DeviceTracksData
VALUES (
    :DataID,
    :DeviceTracksDataDate,
    :DeviceTracksDataValue,
    :DeviceID
);

INSERT INTO InsightMonitors
VALUES (
    :InsightID,
    :Result,
    :InsightMonitorsDate,
    :UserID
);

INSERT INTO InsightProvides
VALUES (
    :InsightID,
    :Result,
    :InsightProvidesDate,
    :DeviceID
);


-- INSERT calories and date for an inputted userID that fetches
INSERT INTO NutritionInputs (NutritionID, DeviceID, UserID, Calories, NutritionInputsDate)
SELECT N.NutritionID, D.DeviceID, U.UserID, :Calories AS Calories, :Date AS NutritionInputsDate
FROM User_table U, NutritionInputs N, Device D
WHERE U.UserID = :UserID AND U.UserID = N.UserID AND N.DeviceID = D.DeviceID;

-- DELETE a USER with all associated data

UPDATE User_table
SET Email = :Email, UserWeight = :UserWeight
WHERE UserID = :UserID;



-- Questions to ask TA
-- 1. Why does the on delete cascades dont work
-- 2. Why does our insert not work?
-- 3. 