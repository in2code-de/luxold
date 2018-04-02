<img align="left" src="../../../Resources/Public/Icons/lux.svg" width="50" />

### CommandController

This part of the documentation shows you all available CommandControllers in Lux.

Every CommandController can be called via CLI or via Scheduler Backend Module (directly or via cronjob).

#### LuxCleanupCommandController

`\In2code\Lux\Command\LuxCleanupCommandController::removeUnknownVisitorsByAgeCommand(int $timestamp)` Remove not
identified visitors where the last visit is older then the given Timestamp (in seconds). Remove means in this case
not deleted=1 but really remove from database.
