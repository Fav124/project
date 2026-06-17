package com.healthmanagement.app.ui.jurusan

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.Jurusan
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class JurusanState(
    val jurusan: List<Jurusan> = emptyList(),
    val isLoading: Boolean = false,
    val error: String? = null
)

class JurusanViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(JurusanState())
    val state: StateFlow<JurusanState> = _state.asStateFlow()

    fun load() {
        viewModelScope.launch {
            _state.value = JurusanState(isLoading = true)
            repository.getJurusan().fold(
                onSuccess = { _state.value = JurusanState(jurusan = it) },
                onFailure = { _state.value = JurusanState(error = it.message) }
            )
        }
    }

    fun create(name: String, description: String) {
        viewModelScope.launch {
            repository.createJurusan(name, description.ifBlank { null }).fold(
                onSuccess = { load() },
                onFailure = { _state.value = _state.value.copy(error = it.message) }
            )
        }
    }

    fun update(id: Int, name: String, description: String) {
        viewModelScope.launch {
            repository.updateJurusan(id, name, description.ifBlank { null }).fold(
                onSuccess = { load() },
                onFailure = { _state.value = _state.value.copy(error = it.message) }
            )
        }
    }

    fun delete(id: Int) {
        viewModelScope.launch {
            repository.deleteJurusan(id).fold(
                onSuccess = { load() },
                onFailure = { _state.value = _state.value.copy(error = it.message) }
            )
        }
    }
}
