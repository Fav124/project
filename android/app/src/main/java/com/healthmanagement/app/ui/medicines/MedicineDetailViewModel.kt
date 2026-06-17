package com.healthmanagement.app.ui.medicines

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.MedicineDetail
import com.healthmanagement.app.data.model.MedicineMutation
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class MedicineDetailState(
    val medicine: MedicineDetail? = null,
    val mutations: List<MedicineMutation>? = null,
    val isLoading: Boolean = false,
    val error: String? = null
)

class MedicineDetailViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(MedicineDetailState())
    val state: StateFlow<MedicineDetailState> = _state.asStateFlow()

    fun load(id: Int) {
        viewModelScope.launch {
            _state.value = MedicineDetailState(isLoading = true)
            repository.getObatDetail(id).fold(
                onSuccess = {
                    _state.value = MedicineDetailState(medicine = it, mutations = it.mutations)
                },
                onFailure = { _state.value = MedicineDetailState(error = it.message) }
            )
        }
    }
}
