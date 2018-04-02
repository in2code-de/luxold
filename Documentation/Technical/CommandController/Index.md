<img align="left" src="../../../Resources/Public/Icons/lux.svg" width="50" />

### CommandController

This part of the documentation shows you all available CommandControllers in Lux.

Every CommandController can be called via CLI or via Scheduler Backend Module (directly or via cronjob).


#### LuxCleanupCommandController

`\In2code\Lux\Command\LuxCleanupCommandController::removeUnknownVisitorsByAgeCommand(int $timestamp)` Remove not
identified visitors where the last visit is older then the given Timestamp (in seconds).
Remove means in this case not deleted=1 but really remove from database.

`\In2code\Lux\Command\LuxCleanupCommandController::removeVisitorsByAgeCommand(int $timestamp)` Remove all visitors
(identified and anonymous) where the last visit is older then the given Timestamp (in seconds).
Remove means in this case not deleted=1 but really remove from database.

`\In2code\Lux\Command\LuxCleanupCommandController::removeVisitorByUidCommand(int $visitorUid)` Remove a single visitor
(identified or anonymous) by a given uid.
Remove means in this case not deleted=1 but really remove from database.


#### LuxServiceCommandController

`\In2code\Lux\Command\LuxServiceCommandController::reCalculateScoringCommand()` This command will calculate the scoring
of all leads. Normally used if the calculation changes or if you are using the variable *lastVisitDaysAgo* in
Extension Manager configuration of the extension Lux. In the last case it's recommended to run the calculation once
per night.

#### LuxAnonymizeCommandController

`\In2code\Lux\Command\LuxAnonymizeCommandController::anonymizeAllCommand()` will anonymous all record data.
This is only a function if you want to present your Lux information to others. Because this function cannot be reverted,
please do this only on test systems.

**Note:** This CommandController can be started from CLI only (not from Scheduler).
