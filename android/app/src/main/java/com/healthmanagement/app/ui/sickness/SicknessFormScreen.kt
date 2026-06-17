package com.healthmanagement.app.ui.sickness

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.healthmanagement.app.ui.components.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SicknessFormScreen(
    sicknessId: Int?,
    onSaved: () -> Unit,
    onBack: () -> Unit,
    viewModel: SicknessFormViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()

    LaunchedEffect(sicknessId) { viewModel.load(sicknessId) }
    LaunchedEffect(state.isSaved) { if (state.isSaved) onSaved() }

    AppScaffold(
        title = if (sicknessId != null) "Edit Kunjungan" else "Kunjungan Baru",
        onBack = onBack
    ) { padding ->
        if (state.isLoading) {
            LoadingIndicator()
        } else {
            Column(
                modifier = Modifier.fillMaxSize().padding(padding).verticalScroll(rememberScrollState()).padding(16.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                // Santri selector
                FormDropdown(
                    label = "Santri",
                    value = state.santriName,
                    items = state.santriList.map { it.name },
                    onSelect = { idx ->
                        state.santriList.getOrNull(idx)?.let { viewModel.onSantriChange(it.id, it.name) }
                    }
                )

                FormField(label = "Tgl Kunjungan", value = state.visitDate, onValueChange = { viewModel.onVisitDateChange(it) }, placeholder = "YYYY-MM-DD")
                FormField(label = "Keluhan", value = state.complaint, onValueChange = { viewModel.onComplaintChange(it) }, singleLine = false, maxLines = 3)
                FormField(label = "Diagnosa", value = state.diagnosis, onValueChange = { viewModel.onDiagnosisChange(it) }, singleLine = false, maxLines = 3)
                FormField(label = "Tindakan", value = state.actionTaken, onValueChange = { viewModel.onActionTakenChange(it) }, singleLine = false, maxLines = 3)
                FormField(label = "Catatan Obat", value = state.medicineNotes, onValueChange = { viewModel.onMedicineNotesChange(it) }, singleLine = false, maxLines = 2)
                FormField(label = "Catatan", value = state.notes, onValueChange = { viewModel.onNotesChange(it) }, singleLine = false, maxLines = 2)

                // Medicines section
                HorizontalDivider()
                Text(text = "Obat yang Diberikan", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.SemiBold)

                state.medicines.forEachIndexed { index, med ->
                    Card(modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp)) {
                        Column(modifier = Modifier.padding(12.dp)) {
                            Row(verticalAlignment = Alignment.CenterVertically) {
                                Text(text = "Obat #${index + 1}", style = MaterialTheme.typography.labelLarge, modifier = Modifier.weight(1f))
                                IconButton(onClick = { viewModel.removeMedicine(index) }) {
                                    Icon(Icons.Default.Delete, contentDescription = "Hapus", tint = MaterialTheme.colorScheme.error)
                                }
                            }
                            FormDropdown(label = "Obat", value = med.medicineName, items = state.medicineList.map { it.name }, onSelect = { idx ->
                                state.medicineList.getOrNull(idx)?.let { viewModel.onMedicineChange(index, it.id, it.name) }
                            })
                            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                                FormField(label = "Dosis", value = med.dosage, onValueChange = { viewModel.onMedicineDosageChange(index, it) }, modifier = Modifier.weight(1f))
                                FormField(label = "Jumlah", value = med.quantity, onValueChange = { viewModel.onMedicineQuantityChange(index, it) }, modifier = Modifier.weight(1f))
                            }
                            FormField(label = "Frekuensi", value = med.frequency, onValueChange = { viewModel.onMedicineFrequencyChange(index, it) })
                        }
                    }
                }

                OutlinedButton(
                    onClick = { viewModel.addMedicine() },
                    modifier = Modifier.fillMaxWidth(),
                    shape = RoundedCornerShape(12.dp)
                ) {
                    Icon(Icons.Default.Add, contentDescription = null)
                    Spacer(modifier = Modifier.width(8.dp))
                    Text("Tambah Obat")
                }

                if (state.error != null) {
                    Text(text = state.error!!, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall)
                }

                Button(
                    onClick = { viewModel.save(sicknessId) },
                    modifier = Modifier.fillMaxWidth().height(50.dp),
                    enabled = !state.isSaving,
                    shape = RoundedCornerShape(12.dp)
                ) {
                    if (state.isSaving) {
                        CircularProgressIndicator(modifier = Modifier.size(20.dp), color = MaterialTheme.colorScheme.onPrimary, strokeWidth = 2.dp)
                    } else {
                        Text("Simpan", style = MaterialTheme.typography.titleMedium)
                    }
                }

                Spacer(modifier = Modifier.height(32.dp))
            }
        }
    }
}

@Composable
private fun FormField(
    label: String,
    value: String,
    onValueChange: (String) -> Unit,
    modifier: Modifier = Modifier,
    singleLine: Boolean = true,
    maxLines: Int = 1,
    placeholder: String = ""
) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        modifier = modifier.fillMaxWidth(),
        singleLine = singleLine,
        maxLines = maxLines,
        shape = RoundedCornerShape(12.dp),
        placeholder = if (placeholder.isNotEmpty()) { { Text(placeholder) } } else null
    )
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun FormDropdown(
    label: String,
    value: String,
    items: List<String>,
    onSelect: (Int) -> Unit,
    modifier: Modifier = Modifier
) {
    var expanded by remember { mutableStateOf(false) }
    ExposedDropdownMenuBox(expanded = expanded, onExpandedChange = { expanded = !expanded }, modifier = modifier) {
        OutlinedTextField(
            value = value,
            onValueChange = {},
            readOnly = true,
            label = { Text(label) },
            trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded) },
            modifier = Modifier.menuAnchor().fillMaxWidth(),
            shape = RoundedCornerShape(12.dp)
        )
        ExposedDropdownMenu(expanded = expanded, onDismissRequest = { expanded = false }) {
            items.forEachIndexed { index, item ->
                DropdownMenuItem(text = { Text(item) }, onClick = { onSelect(index); expanded = false })
            }
        }
    }
}
