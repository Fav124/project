package com.healthmanagement.app.ui.referral

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.Santri
import com.healthmanagement.app.ui.components.*
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class ReferralFormState(
    val isLoading: Boolean = false,
    val isSaving: Boolean = false,
    val isSaved: Boolean = false,
    val error: String? = null,
    val santriId: Int? = null,
    val santriName: String = "",
    val hospitalName: String = "",
    val referralDate: String = "",
    val reason: String = "",
    val diagnosis: String = "",
    val transport: String = "",
    val companionName: String = "",
    val notes: String = "",
    val santriList: List<Santri> = emptyList()
)

@Composable
fun ReferralFormScreen(
    onSaved: () -> Unit,
    onBack: () -> Unit
) {
    val repository = remember { AppRepository() }
    val scope = rememberCoroutineScope()
    val santriList = remember { mutableStateListOf<Santri>() }
    var state by remember { mutableStateOf(ReferralFormState()) }

    LaunchedEffect(Unit) {
        repository.getKunjunganFormData().fold(
            onSuccess = { santriList.addAll(it.santris) },
            onFailure = {}
        )
    }

    LaunchedEffect(state.isSaved) { if (state.isSaved) onSaved() }

    AppScaffold(title = "Buat Rujukan RS", onBack = onBack) { padding ->
        Column(
            modifier = Modifier.fillMaxSize().padding(padding).verticalScroll(rememberScrollState()).padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            DropdownField(label = "Santri", value = state.santriName, items = santriList.map { it.name }) { idx ->
                santriList.getOrNull(idx)?.let { state = state.copy(santriId = it.id, santriName = it.name) }
            }
            FormField(label = "Rumah Sakit", value = state.hospitalName, onValueChange = { state = state.copy(hospitalName = it) })
            FormField(label = "Tgl Rujukan", value = state.referralDate, onValueChange = { state = state.copy(referralDate = it) })
            FormField(label = "Alasan Rujukan", value = state.reason, onValueChange = { state = state.copy(reason = it) }, singleLine = false, maxLines = 3)
            FormField(label = "Diagnosa", value = state.diagnosis, onValueChange = { state = state.copy(diagnosis = it) }, singleLine = false, maxLines = 3)
            FormField(label = "Transport", value = state.transport, onValueChange = { state = state.copy(transport = it) })
            FormField(label = "Pendamping", value = state.companionName, onValueChange = { state = state.copy(companionName = it) })
            FormField(label = "Catatan", value = state.notes, onValueChange = { state = state.copy(notes = it) }, singleLine = false, maxLines = 3)

            if (state.error != null) {
                Text(text = state.error!!, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall)
            }

            Button(
                onClick = {
                    if (state.santriId == null) { state = state.copy(error = "Santri harus dipilih"); return@Button }
                    scope.launch {
                        state = state.copy(isSaving = true, error = null)
                        repository.createRujukan(mapOf(
                            "santri_id" to state.santriId!!,
                            "hospital_name" to state.hospitalName,
                            "referral_date" to state.referralDate,
                            "reason" to state.reason,
                            "diagnosis" to state.diagnosis,
                            "transport" to state.transport,
                            "companion_name" to state.companionName,
                            "notes" to state.notes,
                            "status" to "pending"
                        )).fold(
                            onSuccess = { state = state.copy(isSaving = false, isSaved = true) },
                            onFailure = { state = state.copy(isSaving = false, error = it.message) }
                        )
                    }
                },
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
    modifier: Modifier = Modifier, singleLine: Boolean = true, maxLines: Int = 1
) {
    OutlinedTextField(
        value = value, onValueChange = onValueChange, label = { Text(label) },
        modifier = modifier.fillMaxWidth(), singleLine = singleLine, maxLines = maxLines,
        shape = RoundedCornerShape(12.dp)
    )
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun DropdownField(
    label: String, value: String, items: List<String>, onSelect: (Int) -> Unit
) {
    var expanded by remember { mutableStateOf(false) }
    ExposedDropdownMenuBox(expanded = expanded, onExpandedChange = { expanded = !expanded }) {
        OutlinedTextField(
            value = value, onValueChange = {}, readOnly = true, label = { Text(label) },
            trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded) },
            modifier = Modifier.menuAnchor().fillMaxWidth(), shape = RoundedCornerShape(12.dp)
        )
        ExposedDropdownMenu(expanded = expanded, onDismissRequest = { expanded = false }) {
            items.forEachIndexed { index, item ->
                DropdownMenuItem(text = { Text(item) }, onClick = { onSelect(index); expanded = false })
            }
        }
    }
}
