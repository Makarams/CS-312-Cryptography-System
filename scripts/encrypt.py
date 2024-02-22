from decrypt import decrypt_text
from helper import f, normalize, denormalize

def encrypt_text(plain_text: str, c1: float, c2: float, y_minus_1: float, y_minus_2: float) -> str:
    if len(plain_text) == 0:
        raise Exception("Plain text length must be greater than 0")
    cipher_text = ""
    last = y_minus_1
    second_last = y_minus_2

    decrypt_last = last
    decrypt_second_last = second_last

    for i in range(len(plain_text)):

        c = plain_text[i]
        encrypted = f(normalize(ord(c)) + c1 * last + c2 * second_last)

        denormalized = denormalize(encrypted)
        while True:
            tmp_cipher_text = chr(int(denormalized))
            decrypted_char, tmp_decrypt_last, tmp_decrypt_second_last = decrypt_text(tmp_cipher_text, c1, c2, decrypt_last, decrypt_second_last, True)
            denormalized = (denormalized + ord(c) - ord(decrypted_char)) % 256

            if ord(c) - ord(decrypted_char) == 0:
                break

        decrypt_last = tmp_decrypt_last
        decrypt_second_last = tmp_decrypt_second_last

        cipher_text += chr(int(denormalized))
        second_last = decrypt_second_last
        last = decrypt_last
    # print(cipher_text, end="")
    return cipher_text


def encrypt_audio():
    ...


def encrypt_image():
    ...


def encrypt_video():
    ...
