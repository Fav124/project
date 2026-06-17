package com.healthmanagement.app.ui.santri

import android.content.Context
import android.net.Uri
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.HealthManagementApp
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.model.Kelas
import com.healthmanagement.app.data.model.Jurusan
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import java.io.File
import java.io.FileOutputStream

data class SantriFormState(
    val isLoading: Boolean = false,
    val isSaving: Boolean = false,
    val isSaved: Boolean = false,
    val error: String? = null,
    val isEdit: Boolean = false,
    val editId: Int? = null,
    // Fields
    val nis: String = "",
    val name: String = "",
    val gender: String = "L",
    val birthPlace: String = "",
    val birthDate: String = "",
    val classId: Int? = null,
    val className: String = "",
    val majorId: Int? = null,
    val majorName: String = "",
    val classRoom: String = "",
    val guardianName: String = "",
    val guardianPhone: String = "",
    val guardianRelationship: String = "",
    val bloodType: String = "",
    val height: String = "",
    val weight: String = "",
    val allergies: String = "",
    val medicalHistory: String = "",
    val notes: String = "",
    val photoUri: Uri? = null,
    val kelas: List<Kelas> = emptyList(),
    val jurusan: List<Jurusan> = emptyList()
)

class SantriFormViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(SantriFormState())
    val state: StateFlow<SantriFormState> = _state.asStateFlow()

    fun load(id: Int?) {
        if (id == null) {
            viewModelScope.launch { loadLookups() }
            return
        }
        viewModelScope.launch {
            _state.value = _state.value.copy(isLoading = true, isEdit = true, editId = id)
            loadLookups()
            repository.getSantriDetail(id).fold(
                onSuccess = { santri ->
                    _state.value = _state.value.copy(
                        isLoading = false,
                        nis = santri.nis,
                        name = santri.name,
                        gender = santri.gender ?: "L",
                        birthPlace = santri.birthPlace ?: "",
                        birthDate = santri.birthDate ?: "",
                        classId = santri.classId,
                        className = santri.className ?: "",
                        majorId = santri.majorId,
                        majorName = santri.majorName ?: "",
                        classRoom = santri.classRoom ?: "",
                        guardianName = santri.guardianName ?: "",
                        guardianPhone = santri.guardianPhone ?: "",
                        guardianRelationship = santri.guardianRelationship ?: "",
                        bloodType = santri.bloodType ?: "",
                        height = santri.height ?: "",
                        weight = santri.weight ?: "",
                        allergies = santri.allergies ?: "",
                        medicalHistory = santri.medicalHistory ?: "",
                        notes = santri.notes ?: ""
                    )
                },
                onFailure = { _state.value = _state.value.copy(isLoading = false, error = it.message) }
            )
        }
    }

    private suspend fun loadLookups() {
        repository.getSantriLookups().fold(
            onSuccess = {
                _state.value = _state.value.copy(kelas = it.kelas, jurusan = it.jurusan)
            },
            onFailure = {}
        )
    }

    fun onNisChange(v: String) { _state.value = _state.value.copy(nis = v) }
    fun onNameChange(v: String) { _state.value = _state.value.copy(name = v) }
    fun onBirthPlaceChange(v: String) { _state.value = _state.value.copy(birthPlace = v) }
    fun onBirthDateChange(v: String) { _state.value = _state.value.copy(birthDate = v) }
    fun onClassChange(id: Int, name: String) { _state.value = _state.value.copy(classId = id, className = name) }
    fun onMajorChange(id: Int, name: String) { _state.value = _state.value.copy(majorId = id, majorName = name) }
    fun onClassRoomChange(v: String) { _state.value = _state.value.copy(classRoom = v) }
    fun onGuardianNameChange(v: String) { _state.value = _state.value.copy(guardianName = v) }
    fun onGuardianPhoneChange(v: String) { _state.value = _state.value.copy(guardianPhone = v) }
    fun onGuardianRelationshipChange(v: String) { _state.value = _state.value.copy(guardianRelationship = v) }
    fun onBloodTypeChange(v: String) { _state.value = _state.value.copy(bloodType = v) }
    fun onHeightChange(v: String) { _state.value = _state.value.copy(height = v) }
    fun onWeightChange(v: String) { _state.value = _state.value.copy(weight = v) }
    fun onAllergiesChange(v: String) { _state.value = _state.value.copy(allergies = v) }
    fun onMedicalHistoryChange(v: String) { _state.value = _state.value.copy(medicalHistory = v) }
    fun onNotesChange(v: String) { _state.value = _state.value.copy(notes = v) }
    fun onPhotoSelected(uri: Uri) { _state.value = _state.value.copy(photoUri = uri) }

    fun save() {
        val s = _state.value
        if (s.nis.isBlank() || s.name.isBlank()) {
            _state.value = s.copy(error = "NIS dan Nama harus diisi")
            return
        }
        viewModelScope.launch {
            _state.value = s.copy(isSaving = true, error = null)

            val photoPart = s.photoUri?.let { uri ->
                val context = HealthManagementApp.instance
                val inputStream = context.contentResolver.openInputStream(uri)
                val file = File(context.cacheDir, "upload_photo.jpg")
                inputStream?.let {
                    FileOutputStream(file).use { output -> it.copyTo(output) }
                    it.close()
                }
                val requestBody = file.asRequestBody("image/*".toMediaTypeOrNull())
                MultipartBody.Part.createFormData("photo", file.name, requestBody)
            }

            val result = if (s.isEdit && s.editId != null) {
                repository.updateSantri(
                    id = s.editId, nis = s.nis, name = s.name, gender = s.gender,
                    birthPlace = s.birthPlace, birthDate = s.birthDate,
                    classId = s.classId ?: 0, majorId = s.majorId ?: 0,
                    classRoom = s.classRoom.ifBlank { null },
                    guardianName = s.guardianName.ifBlank { null }, guardianPhone = s.guardianPhone.ifBlank { null },
                    guardianRelationship = s.guardianRelationship.ifBlank { null },
                    bloodType = s.bloodType.ifBlank { null }, height = s.height.ifBlank { null },
                    weight = s.weight.ifBlank { null }, allergies = s.allergies.ifBlank { null },
                    medicalHistory = s.medicalHistory.ifBlank { null }, notes = s.notes.ifBlank { null },
                    photo = photoPart
                )
            } else {
                repository.createSantri(
                    nis = s.nis, name = s.name, gender = s.gender,
                    birthPlace = s.birthPlace, birthDate = s.birthDate,
                    classId = s.classId ?: 0, majorId = s.majorId ?: 0,
                    classRoom = s.classRoom.ifBlank { null },
                    guardianName = s.guardianName.ifBlank { null }, guardianPhone = s.guardianPhone.ifBlank { null },
                    guardianRelationship = s.guardianRelationship.ifBlank { null },
                    bloodType = s.bloodType.ifBlank { null }, height = s.height.ifBlank { null },
                    weight = s.weight.ifBlank { null }, allergies = s.allergies.ifBlank { null },
                    medicalHistory = s.medicalHistory.ifBlank { null }, notes = s.notes.ifBlank { null },
                    photo = photoPart
                )
            }

            result.fold(
                onSuccess = { _state.value = _state.value.copy(isSaving = false, isSaved = true) },
                onFailure = { _state.value = _state.value.copy(isSaving = false, error = it.message) }
            )
        }
    }

}
