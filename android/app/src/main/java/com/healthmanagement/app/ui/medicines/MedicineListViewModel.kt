package com.healthmanagement.app.ui.medicines

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.Medicine
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class MedicineListState(
    val medicines: List<Medicine> = emptyList(),
    val isLoading: Boolean = false,
    val error: String? = null,
    val searchQuery: String = ""
)

class MedicineListViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(MedicineListState())
    val state: StateFlow<MedicineListState> = _state.asStateFlow()

    private var searchJob: Job? = null

    fun load() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true)
            repository.getObat(search = _state.value.searchQuery.ifBlank { null }).fold(
                onSuccess = { _state.value = _state.value.copy(medicines = it.data, isLoading = false) },
                onFailure = { _state.value = _state.value.copy(isLoading = false, error = it.message) }
            )
        }
    }

    fun onSearch(query: String) {
        _state.value = _state.value.copy(searchQuery = query)
        searchJob?.cancel()
        searchJob = viewModelScope.launch { delay(500); load() }
    }
}
