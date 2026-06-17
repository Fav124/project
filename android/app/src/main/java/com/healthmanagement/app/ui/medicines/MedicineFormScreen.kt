package com.healthmanagement.app.ui.medicines

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.healthmanagement.app.ui.components.*

@Composable
fun MedicineFormScreen(
    medicineId: Int?,
    onSaved: () -> Unit,
    onBack: () -> Unit,
    viewModel: MedicineFormViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()

    LaunchedEffect(medicineId) { viewModel.load(medicineId) }
    LaunchedEffect(state.isSaved) { if (state.isSaved) onSaved() }

    AppScaffold(
        title = if (medicineId != null) "Edit Obat" else "Tambah Obat",
        onBack = onBack
    ) { padding ->
        if (state.isLoading) { LoadingIndicator(); return@AppScaffold }
        Column(
            modifier = Modifier.fillMaxSize().padding(padding).verticalScroll(rememberScrollState()).padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            FormField(label = "Kode Obat", value = state.kodeObat, onValueChange = { viewModel.onKodeObatChange(it) })
            FormField(label = "Nama Obat", value = state.name, onValueChange = { viewModel.onNameChange(it) })
            Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                FormField(label = "Kategori", value = state.kategori, onValueChange = { viewModel.onKategoriChange(it) }, modifier = Modifier.weight(1f))
                FormField(label = "Bentuk Sediaan", value = state.bentukSediaan, onValueChange = { viewModel.onBentukSediaanChange(it) }, modifier = Modifier.weight(1f))
            }
            Row(horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                FormField(label = "Stok Awal", value = state.stock, onValueChange = { viewModel.onStockChange(it) }, modifier = Modifier.weight(1f))
                FormField(label = "Min. Stok", value = state.minimumStock, onValueChange = { viewModel.onMinimumStockChange(it) }, modifier = Modifier.weight(1f))
                FormField(label = "Unit", value = state.unit, onValueChange = { viewModel.onUnitChange(it) }, modifier = Modifier.weight(1f))
            }
            FormField(label = "Tgl Kadaluarsa", value = state.expiryDate, onValueChange = { viewModel.onExpiryDateChange(it) }, placeholder = "YYYY-MM-DD")
            FormField(label = "Lokasi Simpan", value = state.lokasiPenyimpanan, onValueChange = { viewModel.onLokasiPenyimpananChange(it) })
            FormField(label = "Deskripsi", value = state.description, onValueChange = { viewModel.onDescriptionChange(it) }, singleLine = false, maxLines = 3)

            if (state.error != null) {
                Text(text = state.error!!, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall)
            }

            Button(
                onClick = { viewModel.save(medicineId) },
                modifier = Modifier.fillMaxWidth().height(50.dp),
                enabled = !state.isSaving,
                shape = RoundedCornerShape(12.dp)
            ) {
                if (state.isSaving) CircularProgressIndicator(modifier = Modifier.size(20.dp), color = MaterialTheme.colorScheme.onPrimary, strokeWidth = 2.dp)
                else Text("Simpan", style = MaterialTheme.typography.titleMedium)
            }
            Spacer(modifier = Modifier.height(32.dp))
        }
    }
}

@Composable
private fun FormField(
    label: String, value: String, onValueChange: (String) -> Unit,
    modifier: Modifier = Modifier, singleLine: Boolean = true, maxLines: Int = 1, placeholder: String = ""
) {
    OutlinedTextField(
        value = value, onValueChange = onValueChange, label = { Text(label) },
        modifier = modifier.fillMaxWidth(), singleLine = singleLine, maxLines = maxLines,
        shape = RoundedCornerShape(12.dp),
        placeholder = if (placeholder.isNotEmpty()) { { Text(placeholder) } } else null
    )
}
