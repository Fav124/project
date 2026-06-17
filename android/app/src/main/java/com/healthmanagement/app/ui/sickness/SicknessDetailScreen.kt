package com.healthmanagement.app.ui.sickness

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
import com.healthmanagement.app.data.model.SicknessCaseDetail
import com.healthmanagement.app.ui.components.*
import com.healthmanagement.app.ui.theme.*

@Composable
fun SicknessDetailScreen(
    sicknessId: Int,
    onEditClick: () -> Unit,
    onBack: () -> Unit,
    viewModel: SicknessDetailViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()

    LaunchedEffect(sicknessId) { viewModel.load(sicknessId) }

    AppScaffold(
        title = "Detail Kunjungan",
        onBack = onBack,
        actions = {
            IconButton(onClick = onEditClick) {
                Icon(Icons.Default.Edit, contentDescription = "Edit")
            }
        }
    ) { padding ->
        when {
            state.isLoading -> LoadingIndicator()
            state.error != null -> EmptyState(message = state.error!!)
            state.case != null -> SicknessDetailContent(
                case = state.case!!,
                modifier = Modifier.padding(padding)
            )
        }
    }
}

@Composable
private fun SicknessDetailContent(
    case: SicknessCaseDetail,
    modifier: Modifier = Modifier
) {
    LazyColumn(
        modifier = modifier.fillMaxSize().padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        item {
            Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(16.dp)) {
                Column(modifier = Modifier.padding(16.dp)) {
                    Row(verticalAlignment = Alignment.CenterVertically) {
                        Column(modifier = Modifier.weight(1f)) {
                            Text(text = case.santri?.name ?: "-", style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.Bold)
                            Text(text = case.santri?.nis ?: "", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                        }
                        StatusBadge(status = case.status)
                    }
                }
            }
        }

        item {
            Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp)) {
                Column(modifier = Modifier.padding(16.dp)) {
                    Text(text = "Informasi Kunjungan", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.SemiBold)
                    Spacer(modifier = Modifier.height(8.dp))
                    DetailRow("Tgl Kunjungan", case.visitDate ?: "-")
                    DetailRow("Keluhan", case.complaint ?: "-")
                    DetailRow("Diagnosa", case.diagnosis ?: "-")
                    DetailRow("Tindakan", case.actionTaken ?: "-")
                    DetailRow("Catatan Obat", case.medicineNotes ?: "-")
                    DetailRow("Catatan", case.notes ?: "-")
                }
            }
        }

        if (case.medicines != null && case.medicines.isNotEmpty()) {
            item {
                SectionHeader(title = "Obat yang Diberikan (${case.medicines.size})")
            }
            items(case.medicines) { med ->
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(12.dp),
                    colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
                    elevation = CardDefaults.cardElevation(defaultElevation = 1.dp)
                ) {
                    Row(
                        modifier = Modifier.fillMaxWidth().padding(12.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Icon(Icons.Default.Medication, contentDescription = null, tint = MaterialTheme.colorScheme.primary)
                        Spacer(modifier = Modifier.width(12.dp))
                        Column(modifier = Modifier.weight(1f)) {
                            Text(text = med.medicineName, style = MaterialTheme.typography.bodyMedium, fontWeight = FontWeight.Medium)
                            Text(text = "${med.dosage ?: "-"} x ${med.quantity} ${med.frequency ?: ""}", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                        }
                        StatusBadge(status = med.status)
                    }
                }
            }
        }

        if (case.status == "handled" || case.status == "referred" || case.status == "rawat_inap") {
            item {
                Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp)) {
                    Column(modifier = Modifier.padding(16.dp)) {
                        Text(text = "Informasi Tindak Lanjut", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.SemiBold)
                        Spacer(modifier = Modifier.height(8.dp))
                        if (case.hospitalName != null) DetailRow("RS Tujuan", case.hospitalName)
                        if (case.transport != null) DetailRow("Transport", case.transport)
                        if (case.companionName != null) DetailRow("Pendamping", case.companionName)
                        if (case.dischargeNotes != null) DetailRow("Catatan Pulang", case.dischargeNotes)
                        if (case.pickedUpBy != null) DetailRow("Dijemput Oleh", case.pickedUpBy)
                        if (case.pickedUpAt != null) DetailRow("Tgl Jemput", case.pickedUpAt)
                    }
                }
            }
        }

        item { Spacer(modifier = Modifier.height(80.dp)) }
    }
}

@Composable
private fun DetailRow(label: String, value: String) {
    Row(modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp)) {
        Text(text = label, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant, modifier = Modifier.width(100.dp))
        Text(text = value, style = MaterialTheme.typography.bodyMedium, modifier = Modifier.weight(1f))
    }
}
