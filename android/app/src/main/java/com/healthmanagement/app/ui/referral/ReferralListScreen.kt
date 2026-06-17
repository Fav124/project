package com.healthmanagement.app.ui.referral

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material.icons.outlined.ClockOutline
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.healthmanagement.app.data.model.HospitalReferral
import com.healthmanagement.app.ui.components.*
import com.healthmanagement.app.ui.theme.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ReferralListScreen(
    onAddClick: () -> Unit,
    onBack: () -> Unit,
    viewModel: ReferralListViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()

    LaunchedEffect(Unit) { viewModel.load() }

    AppScaffold(
        title = "Rujukan RS",
        onBack = onBack,
        actions = {
            IconButton(onClick = onAddClick) {
                Icon(Icons.Default.Add, contentDescription = "Tambah Rujukan")
            }
        }
    ) { padding ->
        Column(
            modifier = Modifier.fillMaxSize().padding(padding).padding(horizontal = 16.dp)
        ) {
            Spacer(modifier = Modifier.height(8.dp))

            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                FilterChip(selected = state.statusFilter == null, onClick = { viewModel.onFilterChange(null) }, label = { Text("Semua") })
                FilterChip(selected = state.statusFilter == "pending", onClick = { viewModel.onFilterChange("pending") }, label = { Text("Pending") })
                FilterChip(selected = state.statusFilter == "ongoing", onClick = { viewModel.onFilterChange("ongoing") }, label = { Text("Diproses") })
                FilterChip(selected = state.statusFilter == "completed", onClick = { viewModel.onFilterChange("completed") }, label = { Text("Selesai") })
            }

            Spacer(modifier = Modifier.height(12.dp))

            when {
                state.isLoading -> LoadingIndicator()
                state.error != null -> EmptyState(message = state.error!!)
                state.referrals.isEmpty() -> EmptyState(message = "Belum ada data rujukan")
                else -> LazyColumn(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    items(state.referrals, key = { it.id }) { referral ->
                        ReferralCard(
                            referral = referral,
                            onUpdateStatus = { status -> viewModel.updateStatus(referral.id, status) },
                            onDelete = { viewModel.delete(referral.id) }
                        )
                    }
                    item { Spacer(modifier = Modifier.height(80.dp)) }
                }
            }
        }
    }
}

@Composable
private fun ReferralCard(
    referral: HospitalReferral,
    onUpdateStatus: (String) -> Unit,
    onDelete: () -> Unit
) {
    var showMenu by remember { mutableStateOf(false) }

    Card(
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
                Icon(Icons.Default.LocalHospital, contentDescription = null, tint = MaterialTheme.colorScheme.primary, modifier = Modifier.size(24.dp))
            }
            Spacer(modifier = Modifier.width(12.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(text = referral.santriName ?: "-", style = MaterialTheme.typography.bodyMedium, fontWeight = FontWeight.Medium)
                Text(text = referral.hospitalName, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                Text(text = referral.referralDate ?: "", style = MaterialTheme.typography.labelSmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
            }
            StatusBadge(status = referral.status)

            Box {
                IconButton(onClick = { showMenu = true }) {
                    Icon(Icons.Default.MoreVert, contentDescription = "Aksi")
                }
                DropdownMenu(expanded = showMenu, onDismissRequest = { showMenu = false }) {
                    if (referral.status != "pending") {
                        DropdownMenuItem(text = { Text("Set Pending") }, onClick = { showMenu = false; onUpdateStatus("pending") }, leadingIcon = { Icon(Icons.Outlined.ClockOutline, contentDescription = null) })
                    }
                    if (referral.status != "ongoing") {
                        DropdownMenuItem(text = { Text("Set Diproses") }, onClick = { showMenu = false; onUpdateStatus("ongoing") }, leadingIcon = { Icon(Icons.Default.PlayCircleOutline, contentDescription = null) })
                    }
                    if (referral.status != "completed") {
                        DropdownMenuItem(text = { Text("Set Selesai") }, onClick = { showMenu = false; onUpdateStatus("completed") }, leadingIcon = { Icon(Icons.Default.CheckCircleOutline, contentDescription = null) })
                    }
                    HorizontalDivider()
                    DropdownMenuItem(text = { Text("Hapus", color = MaterialTheme.colorScheme.error) }, onClick = { showMenu = false; onDelete() }, leadingIcon = { Icon(Icons.Default.Delete, contentDescription = null, tint = MaterialTheme.colorScheme.error) })
                }
            }
        }
    }
}
