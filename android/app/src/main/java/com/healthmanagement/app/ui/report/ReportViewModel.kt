package com.healthmanagement.app.ui.report

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.MedicineReport
import com.healthmanagement.app.data.model.ReportSummary
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class ReportState(
    val healthSummary: ReportSummary? = null,
    val medicineReport: MedicineReport? = null,
    val isLoading: Boolean = false,
    val error: String? = null
)

class ReportViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(ReportState())
    val state: StateFlow<ReportState> = _state.asStateFlow()

    fun load() {
        viewModelScope.launch {
            _state.value = ReportState(isLoading = true)
            val healthResult = repository.getDailySummary(period = "monthly")
            val medResult = repository.getMedicineReport()
            _state.value = ReportState(
                healthSummary = healthResult.getOrNull(),
                medicineReport = medResult.getOrNull(),
                isLoading = false,
                error = if (healthResult.isFailure) healthResult.exceptionOrNull()?.message else null
            )
        }
    }
}
