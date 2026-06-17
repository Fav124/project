package com.healthmanagement.app.ui.jurusan

import androidx.compose.animation.animateColorAsState
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
import androidx.compose.ui.draw.clip
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.healthmanagement.app.data.model.Jurusan
import com.healthmanagement.app.ui.components.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun JurusanScreen(
    onBack: () -> Unit,
    viewModel: JurusanViewModel = viewModel()
) {
    val state by viewModel.state.collectAsState()
    var showDialog by remember { mutableStateOf(false) }
    var editing by remember { mutableStateOf<Jurusan?>(null) }
    var dialogName by remember { mutableStateOf("") }
    var dialogDesc by remember { mutableStateOf("") }

    LaunchedEffect(Unit) { viewModel.load() }

    AppScaffold(
        title = "Data Jurusan",
        onBack = onBack,
        actions = {
            IconButton(onClick = {
                editing = null; dialogName = ""; dialogDesc = ""; showDialog = true
            }) {
                Icon(Icons.Default.Add, contentDescription = "Tambah Jurusan")
            }
        }
    ) { padding ->
        Box(modifier = Modifier.fillMaxSize().padding(padding)) {
            when {
                state.isLoading -> LoadingIndicator()
                state.error != null -> EmptyState(message = state.error!!)
                state.jurusan.isEmpty() -> EmptyState(message = "Belum ada data jurusan")
                else -> LazyColumn(
                    modifier = Modifier.padding(16.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    items(state.jurusan, key = { it.id }) { jurusan ->
                        val dismissState = rememberSwipeToDismissBoxState(
                            confirmValueChange = {
                                if (it == SwipeToDismissBoxValue.EndToStart) {
                                    viewModel.delete(jurusan.id); true
                                } else false
                            }
                        )
                        SwipeToDismissBox(
                            state = dismissState,
                            enableDismissFromStartToEnd = false,
                            backgroundContent = {
                                val color by animateColorAsState(
                                    when (dismissState.targetValue) {
                                        SwipeToDismissBoxValue.EndToStart -> MaterialTheme.colorScheme.error
                                        else -> MaterialTheme.colorScheme.surface
                                    }, label = "bg"
                                )
                                Box(
                                    modifier = Modifier.fillMaxSize().clip(RoundedCornerShape(12.dp)).background(color).padding(horizontal = 20.dp),
                                    contentAlignment = Alignment.CenterEnd
                                ) { Icon(Icons.Default.Delete, contentDescription = null, tint = MaterialTheme.colorScheme.onError) }
                            }
                        ) {
                            Card(
                                onClick = {
                                    editing = jurusan; dialogName = jurusan.name; dialogDesc = jurusan.description ?: ""; showDialog = true
                                },
                                modifier = Modifier.fillMaxWidth(),
                                shape = RoundedCornerShape(12.dp),
                                elevation = CardDefaults.cardElevation(defaultElevation = 1.dp)
                            ) {
                                Row(
                                    modifier = Modifier.fillMaxWidth().padding(16.dp),
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Icon(Icons.Default.AccountTree, contentDescription = null, tint = MaterialTheme.colorScheme.primary)
                                    Spacer(modifier = Modifier.width(12.dp))
                                    Column(modifier = Modifier.weight(1f)) {
                                        Text(text = jurusan.name, style = MaterialTheme.typography.bodyLarge, fontWeight = FontWeight.Medium)
                                        if (jurusan.description != null) {
                                            Text(text = jurusan.description, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.onSurfaceVariant)
                                        }
                                    }
                                    Icon(Icons.Default.Edit, contentDescription = "Edit", tint = MaterialTheme.colorScheme.onSurfaceVariant)
                                }
                            }
                        }
                    }
                    item { Spacer(modifier = Modifier.height(80.dp)) }
                }
            }
        }
    }

    if (showDialog) {
        AlertDialog(
            onDismissRequest = { showDialog = false },
            title = { Text(if (editing != null) "Edit Jurusan" else "Tambah Jurusan") },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    OutlinedTextField(value = dialogName, onValueChange = { dialogName = it }, label = { Text("Nama Jurusan") }, singleLine = true, modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp))
                    OutlinedTextField(value = dialogDesc, onValueChange = { dialogDesc = it }, label = { Text("Deskripsi") }, modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp))
                }
            },
            confirmButton = { Button(onClick = { if (dialogName.isNotBlank()) { if (editing != null) viewModel.update(editing!!.id, dialogName, dialogDesc) else viewModel.create(dialogName, dialogDesc); showDialog = false } }) { Text("Simpan") } },
            dismissButton = { TextButton(onClick = { showDialog = false }) { Text("Batal") } }
        )
    }
}
