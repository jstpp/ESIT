import api.lib as lib

encrypted = lib.encrypt("123^_#gęślą我+=,./", "key123456789qwerty")
decrypted = lib.decrypt(encrypted, "key123456789qwerty")

assert decrypted=="123^_#gęślą我+=,./", decrypted #f"{decrypted}/=123^_#gęślą我+=,./"