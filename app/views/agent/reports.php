<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/agent/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>
    
    <?php flash('report_success'); ?>
    <?php flash('report_error'); ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Report Options</h2>
                
                <form action="<?= URL_ROOT ?>/agent/reports" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="space-y-4">
                        <div>
                            <label for="report_type" class="block text-gray-700 mb-1 font-medium">Report Type</label>
                            <select id="report_type" name="report_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                                <option value="daily_activity" <?= isset($reportType) && $reportType === 'daily_activity' ? 'selected' : '' ?>>Daily Activity</option>
                                <option value="space_utilization" <?= isset($reportType) && $reportType === 'space_utilization' ? 'selected' : '' ?>>Space Utilization</option>
                                <option value="entry_exit" <?= isset($reportType) && $reportType === 'entry_exit' ? 'selected' : '' ?>>Entry/Exit Summary</option>
                                <option value="my_activity" <?= isset($reportType) && $reportType === 'my_activity' ? 'selected' : '' ?>>My Activity Log</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="date" class="block text-gray-700 mb-1 font-medium">Date</label>
                            <input type="date" id="date" name="date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= $date ?? date('Y-m-d') ?>">
                        </div>
                        
                        <div id="time-range" class="<?= isset($reportType) && $reportType === 'entry_exit' ? '' : 'hidden' ?>">
                            <label for="time_range" class="block text-gray-700 mb-1 font-medium">Time Period</label>
                            <select id="time_range" name="time_range" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                                <option value="all_day" <?= isset($timeRange) && $timeRange === 'all_day' ? 'selected' : '' ?>>All Day</option>
                                <option value="morning" <?= isset($timeRange) && $timeRange === 'morning' ? 'selected' : '' ?>>Morning (6AM - 12PM)</option>
                                <option value="afternoon" <?= isset($timeRange) && $timeRange === 'afternoon' ? 'selected' : '' ?>>Afternoon (12PM - 6PM)</option>
                                <option value="evening" <?= isset($timeRange) && $timeRange === 'evening' ? 'selected' : '' ?>>Evening (6PM - 12AM)</option>
                                <option value="night" <?= isset($timeRange) && $timeRange === 'night' ? 'selected' : '' ?>>Night (12AM - 6AM)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="format" class="block text-gray-700 mb-1 font-medium">Format</label>
                            <select id="format" name="format" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                                <option value="chart" <?= isset($format) && $format === 'chart' ? 'selected' : '' ?>>Chart</option>
                                <option value="table" <?= isset($format) && $format === 'table' ? 'selected' : '' ?>>Table</option>
                                <option value="both" <?= isset($format) && $format === 'both' ? 'selected' : '' ?>>Both</option>
                            </select>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg shadow-md transition">
                                <i class="fas fa-chart-bar mr-2"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="bg-blue-50 p-6 rounded-lg shadow-sm mt-6">
                <h3 class="font-semibold text-blue-800 mb-2">Today's Highlights</h3>
                
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-700">Vehicles Entered:</span>
                        <span class="font-semibold"><?= $todayHighlights->vehicles_entered ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">Vehicles Exited:</span>
                        <span class="font-semibold"><?= $todayHighlights->vehicles_exited ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">Current Occupancy:</span>
                        <span class="font-semibold"><?= $todayHighlights->current_occupancy ?? 0 ?> spaces</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">Revenue Today:</span>
                        <span class="font-semibold">$<?= number_format($todayHighlights->revenue_today ?? 0, 2) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">Active Reservations:</span>
                        <span class="font-semibold"><?= $todayHighlights->active_reservations ?? 0 ?></span>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-blue-200">
                    <h4 class="font-semibold text-blue-800 mb-2">My Activity Today</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Entries Processed:</span>
                            <span class="font-semibold"><?= $myActivity->entries_processed ?? 0 ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Exits Processed:</span>
                            <span class="font-semibold"><?= $myActivity->exits_processed ?? 0 ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Reservations Made:</span>
                            <span class="font-semibold"><?= $myActivity->reservations_made ?? 0 ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="md:col-span-2">
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">
                    <?= isset($reportTitle) ? $reportTitle : 'Report Results' ?>
                </h2>
                
                <?php if (isset($noData) && $noData): ?>
                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">
                            <i class="fas fa-chart-area"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-500 mb-2">No Data Available</h3>
                        <p class="text-gray-500">There is no data available for the selected report criteria.</p>
                    </div>
                <?php elseif (isset($reportData)): ?>
                    <?php if ($format === 'chart' || $format === 'both'): ?>
                        <div class="bg-white p-4 rounded-lg border border-gray-200 mb-6">
                            <canvas id="reportChart" height="300"></canvas>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($format === 'table' || $format === 'both'): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <?php foreach ($tableHeaders as $header): ?>
                                            <th class="px-4 py-2 border"><?= $header ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tableData as $row): ?>
                                        <tr class="hover:bg-gray-50">
                                            <?php foreach ($row as $cell): ?>
                                                <td class="px-4 py-2 border"><?= $cell ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (!empty($reportSummary)): ?>
                            <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <h3 class="text-lg font-semibold text-blue-800 mb-2">Summary</h3>
                                <ul class="list-disc pl-5 space-y-1">
                                    <?php foreach ($reportSummary as $item): ?>
                                        <li><?= $item ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div class="mt-6 flex justify-end">
                        <a href="<?= URL_ROOT ?>/agent/printReport/<?= $reportType ?>/<?= $date ?><?= isset($timeRange) ? '/' . $timeRange : '' ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                            <i class="fas fa-print mr-2"></i> Print Report
                        </a>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-500 mb-2">No Report Generated</h3>
                        <p class="text-gray-500">Select report parameters and click "Generate Report" to view results.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle time range selection based on report type
        const reportTypeSelect = document.getElementById('report_type');
        const timeRangeDiv = document.getElementById('time-range');
        
        if (reportTypeSelect && timeRangeDiv) {
            reportTypeSelect.addEventListener('change', function() {
                if (this.value === 'entry_exit') {
                    timeRangeDiv.classList.remove('hidden');
                } else {
                    timeRangeDiv.classList.add('hidden');
                }
            });
        }
        
        <?php if (isset($chartData) && ($format === 'chart' || $format === 'both')): ?>
        // Initialize the chart with the provided data
        const ctx = document.getElementById('reportChart').getContext('2d');
        new Chart(ctx, <?= $chartData ?>);
        <?php endif; ?>
    });
</script>

<?php require APP . 'views/includes/footer.php'; ?>
