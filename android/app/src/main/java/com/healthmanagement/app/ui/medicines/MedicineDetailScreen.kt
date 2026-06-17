package com.healthmanagement.app.ui.medicines

import androidx.compose.foundation.background
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
fun MedicineDetailScreen(
    medicineId: Int,
    onEditClick: () -> Unit,
    onMutationClick: () -> Unit,
    onBack: () -> Unit,
    viewModel: MedicineDetailViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()

    LaunchedEffect(medicineId) { viewModel.load(medicineId) }

    AppScaffold(
        title = "Detail Obat",
        onBack = onBack,
        actions = {
            IconButton(onClick = onMutationClick) {
                Icon(Icons.Default.SwapHoriz, contentDescription = "Mutasi")
            }
            IconButton(onClick = onEditClick) {
                Icon(Icons.Default.Edit, contentDescription = "Edit")
            }
        }
    ) { padding ->
        when {
            state.isLoading -> LoadingIndicator()
            state.error != null -> EmptyState(message = state.error!!)
            state.medicine != null -> MedicineDetailContent(
                state = state,
                modifier = Modifier.padding(padding)
            )
        }
    }
}

@Composable
private fun MedicineDetailContent(
    state: MedicineDetailState,
    modifier: Modifier = Modifier
) {
    val med = state.medicine ?: return
    val isLowStock = med.stock <= med.minimumStock

    LazyColumn(
        modifier = modifier.fillMaxSize().padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        item {
            Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(16.dp)) {
                Column(modifier = Modifier.padding(20.dp), horizontalAlignment = Alignment.CenterHorizontally) {
                    Icon(Icons.Default.Medication, contentDescription = null, modifier = Modifier.size(48.dp), tint = MaterialTheme.colorScheme.primary)
                    Spacer(modifier = Modifier.height(8.dp))
                    Text(text = med.name, style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.Bold)
                    Text(text = med.kodeObat ?: "-", style = MaterialTheme.typography.bodyMedium, color = MaterialTheme.colorScheme.onSurfaceVariant)
                    Spacer(modifier = Modifier.height(12.dp))
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.Center,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally, modifier = Modifier.padding(horizontal = 16.dp)) {
                            Text(text = "${med.stock}", style = MaterialTheme.typography.headlineMedium, fontWeight = FontWeight.Bold, color = if (isLowStock) Red600 else Green600)
                            Text(text = med.unit ?: "Unit", style = MaterialTheme.typography.bodySmall)
                        }
                        Box(modifier = Modifier.width(1.dp).height(40.dp).background(MaterialTheme.colorScheme.outlineVariant))
                        Column(horizontalAlignment = Alignment.CenterHorizontally, modifier = Modifier.padding(horizontal = 16.dp)) {
                            Text(text = "${med.minimumStock}", style = MaterialTheme.typography.headlineMedium, fontWeight = FontWeight.Bold, color = MaterialTheme.colorScheme.onSurfaceVariant)
                            Text(text = "Min. Stok", style = MaterialTheme.typography.bodySmall)
                        }
                    }
                }
            }
        }

        item {
            Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp)) {
                Column(modifier = Modifier.padding(16.dp)) {
                    Text(text = "Informasi Obat", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.SemiBold)
                    Spacer(modifier = Modifier.height(8.dp))
                    DetailRow("Kategori", med.kategori ?: "-")
                    DetailRow("Bentuk Sediaan", med.bentukSediaan ?: "-")
                    DetailRow("Lokasi Simpan", med.lokasiPenyimpanan ?: "-")
                    DetailRow("Kadaluarsa", med.expiryDate ?: "-")
                    DetailRow("Deskripsi", med.description ?: "-")
                }
            }
        }

        if (state.mutations != null && state.mutations.isNotEmpty()) {
            item {
                SectionHeader(title = "Riwayat Mutasi (${state.mutations.size})")
            }
            items(state.mutations) { mutation ->
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(12.dp),
                    elevation = CardDefaults.cardElevation(defaultElevation = 1.dp)
                ) {
                    Row(
                        modifier = Modifier.fillMaxWidth().padding(12.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Box(
                            modifier = Modifier.size(36.dp)
                        ) {
                            Icon(
                                when (mutation.type) {
                                    "in" -> Icons.Default.AddCircle
                                    "out" -> Icons.Default.RemoveCircle
                                    else -> Icons.Default.SwapHoriz
                                },
                                contentDescription = null,
                                tint = when (mutation.type) {
                                    "in" -> Green600; "out" -> Red600; else -> Orange600
                                }
                            )
                        }
                        Spacer(modifier = Modifier.width(12.dp))
                        Column(modifier = Modifier.weight(1f)) {
                            Text(text = mutation.type?.let { if (it == "in") "Stok Masuk" else if (it == "out") "Stok Keluar" else "Penyesuaian" } ?: "-", style = MaterialTheme.typography.bodyMedium, fontWeight = FontWeight.Medium)
                            Text(text = "${mutation.beforeStock} → ${mutation.afterStock} (${if (mutation.amount >= 0) "+" else ""}${mutation.amount})", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                            Text(text = mutation.notes ?: "", style = MaterialTheme.typography.labelSmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                        }
                        Text(text = mutation.createdAt?.take(10) ?: "", style = MaterialTheme.typography.labelSmall)
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
        Text(text = label, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant, modifier = Modifier.width(120.dp))
        Text(text = value, style = MaterialTheme.typography.bodyMedium, modifier = Modifier.weight(1f))
    }
}
