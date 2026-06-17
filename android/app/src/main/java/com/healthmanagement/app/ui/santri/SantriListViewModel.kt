package com.healthmanagement.app.ui.santri

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.Santri
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class SantriListState(
    val santri: List<Santri> = emptyList(),
    val isLoading: Boolean = false,
    val isLoadingMore: Boolean = false,
    val error: String? = null,
    val searchQuery: String = "",
    val currentPage: Int = 1,
    val lastPage: Int = 1
)

class SantriListViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(SantriListState())
    val state: StateFlow<SantriListState> = _state.asStateFlow()

    private var searchJob: Job? = null

    fun loadSantri() {
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, error = null)
            repository.getSantri(page = 1, search = _state.value.searchQuery.ifBlank { null }).fold(
                onSuccess = {
                    _state.value = _state.value.copy(
                        santri = it.data,
                        isLoading = false,
                        currentPage = it.current_page,
                        lastPage = it.last_page
                    )
                },
                onFailure = { _state.value = _state.value.copy(isLoading = false, error = it.message) }
            )
        }
    }

    fun loadMore() {
        val s = _state.value
        if (s.isLoadingMore || s.currentPage >= s.lastPage) return
        viewModelScope.launch {
            _state.value = s.copy(isLoadingMore = true)
            repository.getSantri(page = s.currentPage + 1, search = s.searchQuery.ifBlank { null }).fold(
                onSuccess = {
                    _state.value = _state.value.copy(
                        santri = _state.value.santri + it.data,
                        isLoadingMore = false,
                        currentPage = it.current_page,
                        lastPage = it.last_page
                    )
                },
                onFailure = { _state.value = _state.value.copy(isLoadingMore = false) }
            )
        }
    }

    fun onSearch(query: String) {
        _state.value = _state.value.copy(searchQuery = query)
        searchJob?.cancel()
        searchJob = viewModelScope.launch {
            delay(500)
            loadSantri()
        }
    }

    fun deleteSantri(id: Int) {
        viewModelScope.launch {
            repository.deleteSantri(id).fold(
                onSuccess = { loadSantri() },
                onFailure = { _state.value = _state.value.copy(error = it.message) }
            )
        }
    }
}
