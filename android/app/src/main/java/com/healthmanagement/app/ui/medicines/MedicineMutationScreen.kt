package com.healthmanagement.app.ui.medicines

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.ui.components.AppScaffold
import com.healthmanagement.app.ui.components.LoadingIndicator
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class MutationFormState(
    val isLoading: Boolean = false,
    val isSaving: Boolean = false,
    val isSaved: Boolean = false,
    val error: String? = null,
    val type: String = "in",
    val amount: String = "",
    val notes: String = ""
)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun MedicineMutationScreen(
    medicineId: Int,
    onSaved: () -> Unit,
    onBack: () -> Unit
) {
    val scope = rememberCoroutineScope()
    val repository = remember { AppRepository() }
    var state by remember { mutableStateOf(MutationFormState()) }

    LaunchedEffect(state.isSaved) { if (state.isSaved) onSaved() }

    AppScaffold(title = "Mutasi Stok", onBack = onBack) { padding ->
        Column(
            modifier = Modifier.fillMaxSize().padding(padding).verticalScroll(rememberScrollState()).padding(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            Text(text = "Jenis Mutasi", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.SemiBold)
            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                FilterChip(selected = state.type == "in", onClick = { state = state.copy(type = "in") }, label = { Text("Stok Masuk") }, leadingIcon = { Icon(Icons.Default.AddCircle, contentDescription = null) })
                FilterChip(selected = state.type == "out", onClick = { state = state.copy(type = "out") }, label = { Text("Stok Keluar") }, leadingIcon = { Icon(Icons.Default.RemoveCircle, contentDescription = null) })
                FilterChip(selected = state.type == "adjustment", onClick = { state = state.copy(type = "adjustment") }, label = { Text("Penyesuaian") }, leadingIcon = { Icon(Icons.Default.SwapHoriz, contentDescription = null) })
            }

            OutlinedTextField(value = state.amount, onValueChange = { state = state.copy(amount = it) }, label = { Text("Jumlah") }, singleLine = true, modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp))
            OutlinedTextField(value = state.notes, onValueChange = { state = state.copy(notes = it) }, label = { Text("Catatan") }, modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp), singleLine = false, maxLines = 3)

            if (state.error != null) {
                Text(text = state.error!!, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall)
            }

            Button(
                onClick = {
                    val amount = state.amount.toIntOrNull()
                    if (amount == null || amount <= 0) {
                        state = state.copy(error = "Jumlah harus diisi dengan angka positif")
                        return@Button
                    }
                    scope.launch {
                        state = state.copy(isSaving = true, error = null)
                        repository.createMutasi(mapOf("medicine_id" to medicineId, "type" to state.type, "amount" to amount, "notes" to state.notes)).fold(
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
                else Text("Simpan Mutasi", style = MaterialTheme.typography.titleMedium)
            }
        }
    }
}
