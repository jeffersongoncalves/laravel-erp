<?php

namespace JeffersonGoncalves\Erp\Maintenance\Services;

use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Maintenance\Enums\CompletionStatus;
use JeffersonGoncalves\Erp\Maintenance\Models\MaintenanceSchedule;
use JeffersonGoncalves\Erp\Maintenance\Models\MaintenanceScheduleItem;

/**
 * Expands the rows of a maintenance schedule into concrete, dated visit rows
 * (`maintenance_schedule_details`). Each schedule item produces `no_of_visits`
 * detail rows starting from its `start_date`, stepping by its periodicity.
 */
class MaintenanceScheduleService
{
    /**
     * Generate the dated visit rows for a schedule. Existing generated rows are
     * cleared first so the method is safe to call again after edits.
     */
    public function generateSchedule(MaintenanceSchedule $schedule): void
    {
        $schedule->scheduleDetails()->delete();

        /** @var MaintenanceScheduleItem $item */
        foreach ($schedule->items as $item) {
            $startDate = $item->start_date;

            if ($startDate === null) {
                continue;
            }

            $visits = max(1, $item->no_of_visits);

            for ($step = 0; $step < $visits; $step++) {
                $schedule->scheduleDetails()->create([
                    'item_code' => $item->item_code,
                    'scheduled_date' => $this->nextVisitDate($startDate, $item->periodicity, $step),
                    'completion_status' => CompletionStatus::Pending->value,
                    'serial_no' => $item->serial_no,
                    'sales_person' => $item->sales_person,
                ]);
            }
        }
    }

    /**
     * Step a start date forward by `$step` periods of the given periodicity.
     */
    protected function nextVisitDate(Carbon $startDate, ?string $periodicity, int $step): Carbon
    {
        $date = $startDate->copy();

        return match ($periodicity) {
            'Weekly' => $date->addWeeks($step),
            'Quarterly' => $date->addMonths($step * 3),
            'Yearly' => $date->addYears($step),
            default => $date->addMonths($step),
        };
    }
}
