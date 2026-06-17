package com.healthmanagement.app.ui.santri

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
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
import com.healthmanagement.app.data.model.SantriDetail
import com.healthmanagement.app.data.model.SicknessCase
import com.healthmanagement.app.ui.components.*
import com.healthmanagement.app.ui.theme.*

@Composable
fun SantriDetailScreen(
    santriId: Int,
    onEditClick: () -> Unit,
    onBack: () -> Unit,
    onNavigateToSickness: (Int) -> Unit,
    viewModel: SantriDetailViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()

    LaunchedEffect(santriId) { viewModel.loadSantri(santriId) }

    AppScaffold(
        title = "Detail Santri",
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
            state.santri != null -> SantriDetailContent(
                santri = state.santri!!,
                sicknessCases = state.sicknessCases ?: emptyList(),
                onSicknessClick = onNavigateToSickness,
                modifier = Modifier.padding(padding)
            )
        }
    }
}

@Composable
private fun SantriDetailContent(
    santri: SantriDetail,
    sicknessCases: List<SicknessCase>,
    onSicknessClick: (Int) -> Unit,
    modifier: Modifier = Modifier
) {
    LazyColumn(
        modifier = modifier
            .fillMaxSize()
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        item {
            Card(
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(16.dp)
            ) {
                Column(
                    modifier = Modifier.fillMaxWidth().padding(20.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    Box(
                        modifier = Modifier
                            .size(80.dp)
                            .clip(CircleShape)
                            .background(MaterialTheme.colorScheme.primaryContainer),
                        contentAlignment = Alignment.Center
                    ) {
                        if (santri.photoUrl != null) {
                            // Coil image loading would go here
                            Icon(Icons.Default.Person, contentDescription = null, modifier = Modifier.size(40.dp), tint = MaterialTheme.colorScheme.primary)
                        } else {
                            Text(
                                text = santri.name.take(2).uppercase(),
                                style = MaterialTheme.typography.headlineMedium,
                                fontWeight = FontWeight.Bold,
                                color = MaterialTheme.colorScheme.primary
                            )
                        }
                    }
                    Spacer(modifier = Modifier.height(8.dp))
                    Text(text = santri.name, style = MaterialTheme.typography.titleLarge, fontWeight = FontWeight.Bold)
                    Text(text = santri.nis, style = MaterialTheme.typography.bodyMedium, color = MaterialTheme.colorScheme.onSurfaceVariant)
                    if (santri.className != null) {
                        Text(
                            text = "${santri.className} ${santri.majorName ?: ""}",
                            style = MaterialTheme.typography.bodySmall,
                            color = MaterialTheme.colorScheme.onSurfaceVariant
                        )
                    }
                }
            }
        }

        item {
            SectionHeader(title = "Informasi Pribadi")
            InfoCard(items = listOfNotNull(
                "Jenis Kelamin" to (santri.gender_label ?: "-"),
                "Tempat/Tgl Lahir" to "${santri.birthPlace ?: "-"}, ${santri.birthDate ?: "-"}",
                "Umur" to (santri.age?.toString() ?: "-"),
                "Gol. Darah" to (santri.bloodType ?: "-"),
                "Tinggi/berat" to "${santri.height ?: "-"} / ${santri.weight ?: "-"}",
                "Alergi" to (santri.allergies ?: "-"),
                "Riwayat Medis" to (santri.medicalHistory ?: "-"),
                "Kelas" to (santri.className ?: "-"),
                "Jurusan" to (santri.majorName ?: "-"),
                "Kamar Kelas" to (santri.classRoom ?: "-")
            ))
        }

        item {
            SectionHeader(title = "Informasi Wali")
            InfoCard(items = listOfNotNull(
                "Nama Wali" to (santri.guardianName ?: "-"),
                "No. HP" to (santri.guardianPhone ?: "-"),
                "Hubungan" to (santri.guardianRelationship ?: "-")
            ))
        }

        if (sicknessCases.isNotEmpty()) {
            item {
                SectionHeader(title = "Riwayat Kesehatan (${sicknessCases.size})")
            }
            items(sicknessCases) { case ->
                Card(
                    modifier = Modifier.fillMaxWidth().clickable { onSicknessClick(case.id) },
                    shape = RoundedCornerShape(12.dp),
                    colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surface),
                    elevation = CardDefaults.cardElevation(defaultElevation = 1.dp)
                ) {
                    Row(
                        modifier = Modifier.fillMaxWidth().padding(12.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Column(modifier = Modifier.weight(1f)) {
                            Text(text = case.visitDate ?: "-", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                            Text(text = case.diagnosis ?: case.complaint ?: "-", style = MaterialTheme.typography.bodyMedium, fontWeight = FontWeight.Medium)
                        }
                        StatusBadge(status = case.status)
                    }
                }
            }
        }

        item { Spacer(modifier = Modifier.height(80.dp)) }
    }
}

@Composable
private fun InfoCard(items: List<Pair<String, String>>) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(12.dp)
    ) {
        Column(modifier = Modifier.padding(16.dp)) {
            items.forEachIndexed { index, (label, value) ->
                Row(
                    modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp),
                    horizontalArrangement = Arrangement.SpaceBetween
                ) {
                    Text(text = label, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant, modifier = Modifier.weight(0.4f))
                    Text(text = value, style = MaterialTheme.typography.bodyMedium, fontWeight = FontWeight.Medium, modifier = Modifier.weight(0.6f))
                }
                if (index < items.size - 1) HorizontalDivider(modifier = Modifier.padding(vertical = 4.dp))
            }
        }
    }
}
