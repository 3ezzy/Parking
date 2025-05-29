<?php require APP . 'views/includes/header.php'; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= $title ?></h1>
        <a href="<?= URL_ROOT ?>/admin/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>
    
    <?php flash('report_success'); ?>
    <?php flash('report_error'); ?>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Generate Report</h2>
                
                <form action="<?= URL_ROOT ?>/admin/reports" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="space-y-4">
                        <div>
                            <label for="report_type" class="block text-gray-700 mb-1 font-medium">Report Type</label>
                            <select id="report_type" name="report_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" onchange="toggleDateFields()">
                                <option value="revenue">Revenue Report</option>
                                <option value="occupancy">Space Occupancy Report</option>
                                <option value="vehicle_types">Vehicle Types Report</option>
                                <option value="peak_hours">Peak Hours Report</option>
                                <option value="duration">Average Stay Duration</option>
                            </select>
                        </div>
                        
                        <div id="date-fields">
                            <div>
                                <label for="date_from" class="block text-gray-700 mb-1 font-medium">Date From</label>
                                <input type="date" id="date_from" name="date_from" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= date('Y-m-d', strtotime('-30 days')) ?>">
                            </div>
                            
                            <div class="mt-4">
                                <label for="date_to" class="block text-gray-700 mb-1 font-medium">Date To</label>
                                <input type="date" id="date_to" name="date_to" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        
                        <div>
                            <label for="space_type" class="block text-gray-700 mb-1 font-medium">Space Type (Optional)</label>
                            <select id="space_type" name="space_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                                <option value="">All Types</option>
                                <?php foreach ($spaceTypes as $type): ?>
                                    <option value="<?= $type->id ?>"><?= $type->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="format" class="block text-gray-700 mb-1 font-medium">Format</label>
                            <select id="format" name="format" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                                <option value="chart">Chart</option>
                                <option value="table">Table</option>
                                <option value="both">Both Chart and Table</option>
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
            
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm mt-6">
                <h2 class="text-xl font-semibold text-blue-800 mb-4">Saved Reports</h2>
                
                <?php if (empty($savedReports)): ?>
                    <p class="text-gray-700">No saved reports found.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($savedReports as $report): ?>
                            <div class="border border-gray-200 rounded p-3 bg-white hover:bg-gray-50">
                                <div class="flex justify-between">
                                    <div>
                                        <h3 class="font-semibold"><?= $report->title ?></h3>
                                        <p class="text-sm text-gray-500">Generated: <?= date('M d, Y', strtotime($report->created_at)) ?></p>
                                    </div>
                                    <div>
                                        <a href="<?= URL_ROOT ?>/admin/viewReport/<?= $report->id ?>" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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
                    
                    <div class="mt-6 flex justify-between">
                        <a href="<?= URL_ROOT ?>/admin/exportReport/<?= $reportType ?>/<?= $dateFrom ?>/<?= $dateTo ?>/<?= $spaceTypeId ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                            <i class="fas fa-file-export mr-2"></i> Export to CSV
                        </a>
                        
                        <form action="<?= URL_ROOT ?>/admin/saveReport" method="POST" class="flex">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="report_type" value="<?= $reportType ?>">
                            <input type="hidden" name="date_from" value="<?= $dateFrom ?>">
                            <input type="hidden" name="date_to" value="<?= $dateTo ?>">
                            <input type="hidden" name="space_type" value="<?= $spaceTypeId ?>">
                            
                            <input type="text" name="report_title" placeholder="Report title" class="px-4 py-2 border rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-r-lg transition">
                                <i class="fas fa-save"></i>
                            </button>
                        </form>
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
    function toggleDateFields() {
        // This function could be expanded to show/hide specific fields based on report type
        const reportType = document.getElementById('report_type').value;
        const dateFields = document.getElementById('date-fields');
        
        // All report types currently use date fields, but this could be modified if needed
        dateFields.style.display = 'block';
    }
    
    <?php if (isset($chartData) && ($format === 'chart' || $format === 'both')): ?>
    // Initialize the chart with the provided data
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('reportChart').getContext('2d');
        new Chart(ctx, <?= $chartData ?>);
    });
    <?php endif; ?>
</script>

<?php require APP . 'views/includes/footer.php'; ?>
