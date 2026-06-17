package com.healthmanagement.app.data.api

import android.content.Context
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import com.healthmanagement.app.HealthManagementApp
import com.healthmanagement.app.dataStore
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.flow.map
import kotlinx.coroutines.runBlocking

object SessionManager {
    private val TOKEN_KEY = stringPreferencesKey("auth_token")
    private val USER_KEY = stringPreferencesKey("user_data")
    private val ROLE_KEY = stringPreferencesKey("user_role")

    private val _isLoggedIn = MutableStateFlow<Boolean?>(null)
    val isLoggedIn: StateFlow<Boolean?> = _isLoggedIn.asStateFlow()

    private val _token = MutableStateFlow<String?>(null)
    val token: StateFlow<String?> = _token.asStateFlow()

    private val _userRole = MutableStateFlow<String?>(null)
    val userRole: StateFlow<String?> = _userRole.asStateFlow()

    private val _userData = MutableStateFlow<String?>(null)
    val userData: StateFlow<String?> = _userData.asStateFlow()

    private val dataStore get() = HealthManagementApp.instance.dataStore

    fun initialize() {
        runBlocking {
            val savedToken = dataStore.data.map { it[TOKEN_KEY] }.first()
            val savedRole = dataStore.data.map { it[ROLE_KEY] }.first()
            val savedUser = dataStore.data.map { it[USER_KEY] }.first()

            _token.value = savedToken
            _userRole.value = savedRole
            _userData.value = savedUser
            _isLoggedIn.value = savedToken != null
        }
    }

    suspend fun saveSession(token: String, role: String, userDataJson: String) {
        dataStore.edit {
            it[TOKEN_KEY] = token
            it[ROLE_KEY] = role
            it[USER_KEY] = userDataJson
        }
        _token.value = token
        _userRole.value = role
        _userData.value = userDataJson
        _isLoggedIn.value = true
    }

    suspend fun logout() {
        dataStore.edit {
            it.remove(TOKEN_KEY)
            it.remove(ROLE_KEY)
            it.remove(USER_KEY)
        }
        _token.value = null
        _userRole.value = null
        _userData.value = null
        _isLoggedIn.value = false
    }
}
