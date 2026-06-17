package com.healthmanagement.app.ui.kelas

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.Kelas
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class KelasState(
    val kelas: List<Kelas> = emptyList(),
    val isLoading: Boolean = false,
    val error: String? = null
)

class KelasViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(KelasState())
    val state: StateFlow<KelasState> = _state.asStateFlow()

    fun load() {
        viewModelScope.launch {
            _state.value = KelasState(isLoading = true)
            repository.getKelas().fold(
                onSuccess = { _state.value = KelasState(kelas = it) },
                onFailure = { _state.value = KelasState(error = it.message) }
            )
        }
    }

    fun create(name: String, description: String) {
        viewModelScope.launch {
            repository.createKelas(name, description.ifBlank { null }).fold(
                onSuccess = { load() },
                onFailure = { _state.value = _state.value.copy(error = it.message) }
            )
        }
    }

    fun update(id: Int, name: String, description: String) {
        viewModelScope.launch {
            repository.updateKelas(id, name, description.ifBlank { null }).fold(
                onSuccess = { load() },
                onFailure = { _state.value = _state.value.copy(error = it.message) }
            )
        }
    }

    fun delete(id: Int) {
        viewModelScope.launch {
            repository.deleteKelas(id).fold(
                onSuccess = { load() },
                onFailure = { _state.value = _state.value.copy(error = it.message) }
            )
        }
    }
}
