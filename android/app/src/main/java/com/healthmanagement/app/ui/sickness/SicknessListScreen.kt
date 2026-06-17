package com.healthmanagement.app.ui.sickness

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
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
import androidx.compose.ui.draw.clip
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.healthmanagement.app.data.model.SicknessCase
import com.healthmanagement.app.ui.components.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SicknessListScreen(
    onSicknessClick: (Int) -> Unit,
    onAddClick: () -> Unit,
    onBack: () -> Unit,
    viewModel: SicknessListViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()

    LaunchedEffect(Unit) { viewModel.load() }

    AppScaffold(
        title = "Santri Sakit",
        onBack = onBack,
        actions = {
            IconButton(onClick = onAddClick) {
                Icon(Icons.Default.Add, contentDescription = "Tambah")
            }
        }
    ) { padding ->
        Column(
            modifier = Modifier.fillMaxSize().padding(padding).padding(horizontal = 16.dp)
        ) {
            Spacer(modifier = Modifier.height(8.dp))

            SearchBar(
                query = state.searchQuery,
                onQueryChange = { viewModel.onSearch(it) },
                placeholder = "Cari santri sakit..."
            )

            Spacer(modifier = Modifier.height(8.dp))

            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                FilterChip(
                    selected = state.statusFilter == null,
                    onClick = { viewModel.onFilterChange(null) },
                    label = { Text("Semua") }
                )
                FilterChip(
                    selected = state.statusFilter == "observed",
                    onClick = { viewModel.onFilterChange("observed") },
                    label = { Text("Observasi") }
                )
                FilterChip(
                    selected = state.statusFilter == "handled",
                    onClick = { viewModel.onFilterChange("handled") },
                    label = { Text("Ditangani") }
                )
                FilterChip(
                    selected = state.statusFilter == "recovered",
                    onClick = { viewModel.onFilterChange("recovered") },
                    label = { Text("Sembuh") }
                )
            }

            Spacer(modifier = Modifier.height(12.dp))

            when {
                state.isLoading && state.cases.isEmpty() -> LoadingIndicator()
                state.error != null -> EmptyState(message = state.error!!)
                state.cases.isEmpty() -> EmptyState(message = "Belum ada data")
                else -> LazyColumn(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    items(state.cases, key = { it.id }) { case ->
                        SicknessCaseCard(case = case, onClick = { onSicknessClick(case.id) })
                    }
                    item { Spacer(modifier = Modifier.height(80.dp)) }
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun SicknessCaseCard(case: SicknessCase, onClick: () -> Unit) {
    Card(
        onClick = onClick,
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(12.dp),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
    ) {
        Row(
            modifier = Modifier.fillMaxWidth().padding(12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier.size(44.dp).clip(RoundedCornerShape(12.dp)).background(MaterialTheme.colorScheme.primaryContainer),
                contentAlignment = Alignment.Center
            ) {
                Icon(Icons.Default.MedicalServices, contentDescription = null, tint = MaterialTheme.colorScheme.primary, modifier = Modifier.size(24.dp))
            }
            Spacer(modifier = Modifier.width(12.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(text = case.santriName ?: "-", style = MaterialTheme.typography.bodyMedium, fontWeight = FontWeight.Medium)
                Text(text = case.diagnosis ?: case.complaint ?: "-", style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                Text(text = case.visitDate ?: "", style = MaterialTheme.typography.labelSmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
            }
            StatusBadge(status = case.status)
        }
    }
}
