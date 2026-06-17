package com.healthmanagement.app.ui.sickness

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.SicknessCase
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class SicknessListState(
    val cases: List<SicknessCase> = emptyList(),
    val isLoading: Boolean = false,
    val error: String? = null,
    val searchQuery: String = "",
    val statusFilter: String? = null
)

class SicknessListViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(SicknessListState())
    val state: StateFlow<SicknessListState> = _state.asStateFlow()

    private var searchJob: Job? = null

    fun load() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true)
            repository.getKunjungan(search = _state.value.searchQuery.ifBlank { null }, status = _state.value.statusFilter).fold(
                onSuccess = { _state.value = _state.value.copy(cases = it.data, isLoading = false) },
                onFailure = { _state.value = _state.value.copy(isLoading = false, error = it.message) }
            )
        }
    }

    fun onSearch(query: String) {
        _state.value = _state.value.copy(searchQuery = query)
        searchJob?.cancel()
        searchJob = viewModelScope.launch { delay(500); load() }
    }

    fun onFilterChange(status: String?) {
        _state.value = _state.value.copy(statusFilter = status)
        load()
    }
}
