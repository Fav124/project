package com.healthmanagement.app.ui.auth

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.healthmanagement.app.data.api.AppRepository
import com.healthmanagement.app.data.api.SessionManager
import com.google.gson.Gson
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class AuthState(
    val email: String = "",
    val password: String = "",
    val name: String = "",
    val noHp: String = "",
    val isLoading: Boolean = false,
    val isSuccess: Boolean = false,
    val isRegistered: Boolean = false,
    val error: String? = null
)

class AuthViewModel : ViewModel() {
    private val repository = AppRepository()

    private val _state = MutableStateFlow(AuthState())
    val state: StateFlow<AuthState> = _state.asStateFlow()

    fun onEmailChange(email: String) { _state.value = _state.value.copy(email = email, error = null) }
    fun onPasswordChange(password: String) { _state.value = _state.value.copy(password = password, error = null) }
    fun onNameChange(name: String) { _state.value = _state.value.copy(name = name, error = null) }
    fun onNoHpChange(noHp: String) { _state.value = _state.value.copy(noHp = noHp, error = null) }

    fun login() {
        val s = _state.value
        if (s.email.isBlank() || s.password.isBlank()) {
            _state.value = s.copy(error = "Email dan password harus diisi")
            return
        }
        viewModelScope.launch {
            _state.value = s.copy(isLoading = true, error = null)
            repository.login(s.email, s.password).fold(
                onSuccess = { response ->
                    SessionManager.saveSession(
                        response.token,
                        response.user.role,
                        Gson().toJson(response.user)
                    )
                    _state.value = _state.value.copy(isLoading = false, isSuccess = true)
                },
                onFailure = { e ->
                    _state.value = _state.value.copy(isLoading = false, error = e.message ?: "Login gagal")
                }
            )
        }
    }

    fun register() {
        val s = _state.value
        if (s.name.isBlank() || s.email.isBlank() || s.password.isBlank()) {
            _state.value = s.copy(error = "Semua field harus diisi")
            return
        }
        viewModelScope.launch {
            _state.value = s.copy(isLoading = true, error = null)
            repository.register(s.name, s.email, s.password, s.noHp).fold(
                onSuccess = {
                    _state.value = _state.value.copy(isLoading = false, isRegistered = true)
                },
                onFailure = { e ->
                    _state.value = _state.value.copy(isLoading = false, error = e.message ?: "Pendaftaran gagal")
                }
            )
        }
    }
}
