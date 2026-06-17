package com.healthmanagement.app.ui.report

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.healthmanagement.app.ui.components.*
import com.healthmanagement.app.ui.theme.*

@Composable
fun ReportScreen(
    onBack: () -> Unit,
    viewModel: ReportViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()
    var selectedTab by remember { mutableIntStateOf(0) }

    LaunchedEffect(Unit) { viewModel.load() }

    AppScaffold(title = "Laporan", onBack = onBack) { padding ->
        Column(modifier = Modifier.fillMaxSize().padding(padding).padding(16.dp)) {
            TabRow(selectedTabIndex = selectedTab) {
                Tab(selected = selectedTab == 0, onClick = { selectedTab = 0 }, text = { Text("Kesehatan") })
                Tab(selected = selectedTab == 1, onClick = { selectedTab = 1 }, text = { Text("Obat") })
            }

            Spacer(modifier = Modifier.height(12.dp))

            if (state.isLoading) {
                LoadingIndicator()
            } else if (state.error != null) {
                EmptyState(message = state.error!!)
            } else when (selectedTab) {
                0 -> HealthReport(state)
                1 -> MedicineReportTab(state)
            }
        }
    }
}

@Composable
private fun HealthReport(state: ReportState) {
    val summary = state.healthSummary ?: return
    LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp)) {
        item {
            Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                StatCard(title = "Total Kasus", value = summary.totalCases.toString(), icon = Icons.Default.MedicalServices, color = Teal600, modifier = Modifier.weight(1f))
                StatCard(title = "Sembuh", value = summary.totalRecovered.toString(), icon = Icons.Default.CheckCircle, color = Green600, modifier = Modifier.weight(1f))
            }
        }
        item {
            Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                StatCard(title = "Dirujuk", value = summary.totalReferred.toString(), icon = Icons.Default.LocalHospital, color = Orange600, modifier = Modifier.weight(1f))
                StatCard(title = "Obat Digunakan", value = summary.totalMedicinesUsed.toString(), icon = Icons.Default.Medication, color = Blue600, modifier = Modifier.weight(1f))
            }
        }

        if (summary.topDiagnoses != null && summary.topDiagnoses.isNotEmpty()) {
            item { SectionHeader(title = "Diagnosa Terbanyak") }
            items(summary.topDiagnoses.take(5)) { diag ->
                Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp), elevation = CardDefaults.cardElevation(defaultElevation = 1.dp)) {
                    Row(modifier = Modifier.fillMaxWidth().padding(12.dp), verticalAlignment = Alignment.CenterVertically) {
                        Text(text = diag.diagnosis, modifier = Modifier.weight(1f), style = MaterialTheme.typography.bodyMedium)
                        Text(text = diag.total.toString(), style = MaterialTheme.typography.bodyLarge, fontWeight = FontWeight.Bold, color = MaterialTheme.colorScheme.primary)
                    }
                }
            }
        }

        if (summary.monthlyData != null && summary.monthlyData.isNotEmpty()) {
            item { SectionHeader(title = "Tren Bulanan") }
            items(summary.monthlyData) { month ->
                Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp), elevation = CardDefaults.cardElevation(defaultElevation = 1.dp)) {
                    Row(modifier = Modifier.fillMaxWidth().padding(12.dp), verticalAlignment = Alignment.CenterVertically) {
                        Text(text = month.month ?: "-", modifier = Modifier.weight(1f), style = MaterialTheme.typography.bodyMedium)
                        Text(text = month.total.toString(), style = MaterialTheme.typography.bodyLarge, fontWeight = FontWeight.Bold, color = MaterialTheme.colorScheme.primary)
                    }
                }
            }
        }

        item { Spacer(modifier = Modifier.height(80.dp)) }
    }
}

@Composable
private fun MedicineReportTab(state: ReportState) {
    val report = state.medicineReport ?: return
    LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp)) {
        item {
            Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                StatCard(title = "Total Obat", value = report.totalMedicines.toString(), icon = Icons.Default.Medication, color = Teal600, modifier = Modifier.weight(1f))
                StatCard(title = "Total Mutasi", value = report.totalMutations.toString(), icon = Icons.Default.SwapHoriz, color = Blue600, modifier = Modifier.weight(1f))
            }
        }
        item {
            Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                StatCard(title = "Stok Masuk", value = report.totalIn.toString(), icon = Icons.Default.AddCircle, color = Green600, modifier = Modifier.weight(1f))
                StatCard(title = "Stok Keluar", value = report.totalOut.toString(), icon = Icons.Default.RemoveCircle, color = Red600, modifier = Modifier.weight(1f))
            }
        }
        item { StatCard(title = "Obat Hampir Habis", value = report.lowStockCount.toString(), icon = Icons.Default.Warning, color = Orange600) }

        if (report.usageData != null && report.usageData.isNotEmpty()) {
            item { SectionHeader(title = "Penggunaan Obat") }
            items(report.usageData) { usage ->
                Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp), elevation = CardDefaults.cardElevation(defaultElevation = 1.dp)) {
                    Row(modifier = Modifier.fillMaxWidth().padding(12.dp), verticalAlignment = Alignment.CenterVertically) {
                        Text(text = usage.medicineName, modifier = Modifier.weight(1f), style = MaterialTheme.typography.bodyMedium)
                        Text(text = usage.totalUsed.toString(), style = MaterialTheme.typography.bodyLarge, fontWeight = FontWeight.Bold, color = MaterialTheme.colorScheme.primary)
                    }
                }
            }
        }

        item { Spacer(modifier = Modifier.height(80.dp)) }
    }
}
