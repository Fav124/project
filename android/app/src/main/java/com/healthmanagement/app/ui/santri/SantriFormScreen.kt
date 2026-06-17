package com.healthmanagement.app.ui.santri

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.healthmanagement.app.ui.components.*

@Composable
fun SantriFormScreen(
    santriId: Int?,
    onSaved: () -> Unit,
    onBack: () -> Unit,
    viewModel: SantriFormViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()

    LaunchedEffect(santriId) { viewModel.load(santriId) }
    LaunchedEffect(state.isSaved) { if (state.isSaved) onSaved() }

    val imagePicker = rememberLauncherForActivityResult(
        contract = ActivityResultContracts.GetContent()
    ) { uri: Uri? -> uri?.let { viewModel.onPhotoSelected(it) } }

    AppScaffold(
        title = if (santriId != null) "Edit Santri" else "Tambah Santri",
        onBack = onBack
    ) { padding ->
        if (state.isLoading) {
            LoadingIndicator()
        } else {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(padding)
                    .verticalScroll(rememberScrollState())
                    .padding(16.dp),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                // Photo
                Box(
                    modifier = Modifier.fillMaxWidth(),
                    contentAlignment = Alignment.Center
                ) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Box(
                            modifier = Modifier
                                .size(80.dp)
                                .clip(CircleShape)
                                .background(MaterialTheme.colorScheme.primaryContainer)
                                .clickable { imagePicker.launch("image/*") },
                            contentAlignment = Alignment.Center
                        ) {
                            Icon(Icons.Default.CameraAlt, contentDescription = "Pilih Foto", tint = MaterialTheme.colorScheme.primary, modifier = Modifier.size(32.dp))
                        }
                        Spacer(modifier = Modifier.height(4.dp))
                        Text(text = "Tap untuk upload foto", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                    }
                }

                FormField(label = "NIS", value = state.nis, onValueChange = { viewModel.onNisChange(it) })
                FormField(label = "Nama Lengkap", value = state.name, onValueChange = { viewModel.onNameChange(it) })

                Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                    FormField(label = "Tempat Lahir", value = state.birthPlace, onValueChange = { viewModel.onBirthPlaceChange(it) }, modifier = Modifier.weight(1f))
                    FormField(label = "Tgl Lahir", value = state.birthDate, onValueChange = { viewModel.onBirthDateChange(it) }, modifier = Modifier.weight(1f), placeholder = "YYYY-MM-DD")
                }

                Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                    FormDropdown(label = "Kelas", value = state.className, items = state.kelas.map { it.name }, onSelect = { idx ->
                        state.kelas.getOrNull(idx)?.let { viewModel.onClassChange(it.id, it.name) }
                    }, modifier = Modifier.weight(1f))
                    FormDropdown(label = "Jurusan", value = state.majorName, items = state.jurusan.map { it.name }, onSelect = { idx ->
                        state.jurusan.getOrNull(idx)?.let { viewModel.onMajorChange(it.id, it.name) }
                    }, modifier = Modifier.weight(1f))
                }

                FormField(label = "Kamar Kelas", value = state.classRoom, onValueChange = { viewModel.onClassRoomChange(it) })

                HorizontalDivider()
                Text(text = "Informasi Wali", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.SemiBold)

                FormField(label = "Nama Wali", value = state.guardianName, onValueChange = { viewModel.onGuardianNameChange(it) })
                FormField(label = "No. HP Wali", value = state.guardianPhone, onValueChange = { viewModel.onGuardianPhoneChange(it) })
                FormField(label = "Hubungan", value = state.guardianRelationship, onValueChange = { viewModel.onGuardianRelationshipChange(it) })

                HorizontalDivider()
                Text(text = "Informasi Medis", style = MaterialTheme.typography.titleMedium, fontWeight = FontWeight.SemiBold)

                Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                    FormField(label = "Gol. Darah", value = state.bloodType, onValueChange = { viewModel.onBloodTypeChange(it) }, modifier = Modifier.weight(1f))
                    FormField(label = "Tinggi (cm)", value = state.height, onValueChange = { viewModel.onHeightChange(it) }, modifier = Modifier.weight(1f))
                    FormField(label = "Berat (kg)", value = state.weight, onValueChange = { viewModel.onWeightChange(it) }, modifier = Modifier.weight(1f))
                }

                FormField(label = "Alergi", value = state.allergies, onValueChange = { viewModel.onAllergiesChange(it) })
                FormField(label = "Riwayat Medis", value = state.medicalHistory, onValueChange = { viewModel.onMedicalHistoryChange(it) }, singleLine = false, maxLines = 3)
                FormField(label = "Catatan", value = state.notes, onValueChange = { viewModel.onNotesChange(it) }, singleLine = false, maxLines = 3)

                if (state.error != null) {
                    Text(text = state.error!!, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.bodySmall)
                }

                Spacer(modifier = Modifier.height(8.dp))

                Button(
                    onClick = { viewModel.save() },
                    modifier = Modifier.fillMaxWidth().height(50.dp),
                    enabled = !state.isSaving,
                    shape = RoundedCornerShape(12.dp)
                ) {
                    if (state.isSaving) {
                        CircularProgressIndicator(modifier = Modifier.size(20.dp), color = MaterialTheme.colorScheme.onPrimary, strokeWidth = 2.dp)
                    } else {
                        Text(if (santriId != null) "Simpan Perubahan" else "Simpan", style = MaterialTheme.typography.titleMedium)
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

    ExposedDropdownMenuBox(
        expanded = expanded,
        onExpandedChange = { expanded = !expanded },
        modifier = modifier
    ) {
        OutlinedTextField(
            value = value,
            onValueChange = {},
            readOnly = true,
            label = { Text(label) },
            trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(expanded) },
            modifier = Modifier.menuAnchor().fillMaxWidth(),
            shape = RoundedCornerShape(12.dp)
        )
        ExposedDropdownMenu(
            expanded = expanded,
            onDismissRequest = { expanded = false }
        ) {
            items.forEachIndexed { index, item ->
                DropdownMenuItem(
                    text = { Text(item) },
                    onClick = {
                        onSelect(index)
                        expanded = false
                    }
                )
            }
        }
    }
}
