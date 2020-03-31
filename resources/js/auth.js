'use strict';

/**
 * Класс для получения авторизационных данных пользователя
 * (синглтон) не используем cors, jwt токены, на беке используем роутинг с включенными куками и сессией
 */
class Auth {
    constructor() {
        if (typeof Auth.instance === 'object') {
            return Auth.instance;
        }
        Auth.instance = this;
        return this;
    }

    setUser(user) {
        if (user !== null) {
            this.user = user;
            this.check = true;
        }

        return this;
    }

    getUser() {
        return this.user;
    }
}

export const auth = new Auth();
