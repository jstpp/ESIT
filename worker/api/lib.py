import requests
import api.config.config as apiconfig
import json
from Crypto.Cipher import AES
import base64, shutil, os, time, socket, zipfile, io

def encrypt(data, key):
    try:
        result = []
        cipher = AES.new((key[:16].encode('utf-8')), AES.MODE_GCM)
        ciphertext, tag = cipher.encrypt_and_digest(data.encode('utf-8'))
        result.append(ciphertext)
        result.append(cipher.nonce)
        result.append(tag)
        return result
    except Exception as exception:
        print(f"EXCEPTION | lib.py: encrypt(): {exception}")
        return False

def decrypt(data, key):
    try:
        cipher = AES.new((key[:16].encode('utf-8')), AES.MODE_GCM, data[1].encode('utf-8'))
        result = cipher.decrypt(data[0]).decode('utf-8')
        return result
    except Exception as exception:
        print(f"EXCEPTION | lib.py: decrypt(): {exception}")
        return False

def decode_and_extract_zip(b64string, output_dir):
    try:
        zip_bytes = base64.b64decode(b64string)
        zip_stream = io.BytesIO(zip_bytes)

        with zipfile.ZipFile(zip_stream, 'r') as zip_ref:
            zip_ref.extractall(output_dir)                  #It can throw an exception when worker is not trusted and api returns some html elements except of encoded file.
    except Exception as exception:
        print(f"EXCEPTION | lib.py: decode_and_extract_zip(): {exception}")
        return False
    else:
        return True

def ask_for_inout(data):
    try:
        data["worker_name"] = socket.gethostname()
        data["worker_addr"] = socket.gethostbyname(socket.gethostname())

        print(str(time.ctime())+f' | Asked {data["listenerUrl"]} for inout of problem {data["problem_id"]}...')
        encrypted = encrypt(json.dumps(data), apiconfig.network_private_key)
        data_to_send = json.loads('{"content":"", "nonce":""}')
        data_to_send['content'] = base64.b64encode(encrypted[0]).decode('utf-8')
        data_to_send['nonce'] = base64.b64encode(encrypted[1]).decode('utf-8')
        data_to_send['tag'] = base64.b64encode(encrypted[2]).decode('utf-8')

        headers={"Content-Type": "application/json; charset=utf-8"}

        api_connection = requests.post(data['listenerUrl'] + "/app/process.php?r=ask_for_inout", json=data_to_send, headers=headers)
        print("API response:", api_connection.content)
        decode_and_extract_zip(api_connection.content, str(os.path.dirname(os.path.realpath(__file__))) + "/../inout/" + data['problem_id'])

        return True
    except Exception as exception:
        print(f"EXCEPTION | lib.py: ask_for_inout(): {exception}")
        return False

def prepare(data):
    try:
        print(str(time.ctime())+f' | Unpacking user\'s submission to problem {data["problem_id"]} received from {data["listenerUrl"]}...')
        decode_and_extract_zip(data['submission_file'], str(os.path.dirname(os.path.realpath(__file__))) + "/../solutions/" + data['submission_id'])
        return True
    except Exception as exception:
        print(f"EXCEPTION | lib.py: prepare(): {exception}")
        return False

def prepare_base64(data):
    try:
        with open(str(os.path.dirname(os.path.realpath(__file__)))+"/../solutions/"+str(data['submission_id'])+"/"+str(data['submission_id'])+".zip", 'rb') as f:
            zip_bytes = f.read()
            encoded_zip = base64.b64encode(zip_bytes).decode('utf-8')
        return encoded_zip
    except Exception as exception:
        print(f"EXCEPTION | lib.py: prepare_base64(): {exception}")
        return False

def prepare_file_transfer(dir_id):
    try:
        shutil.make_archive(str(os.path.dirname(os.path.realpath(__file__)))+"/../solutions/"+str(dir_id)+"/"+str(dir_id), 'zip', str(os.path.dirname(os.path.realpath(__file__)))+"/../solutions/"+str(dir_id))
    except Exception as exception:
        print(f"EXCEPTION | lib.py: prepare_file_transfer(): {exception}")
        return False
    else:
        return True

def send(status, data):
    try:
        data_to_decrypt = json.loads('{"content":"", "nonce":""}')
        headers={"Content-Type": "application/json; charset=utf-8"}

        if prepare_file_transfer(data['submission_id']):
            data['submission_file'] = prepare_base64(data)
            data['status'] = status
            encrypted = encrypt(json.dumps(data), apiconfig.network_private_key)
            data_to_decrypt['content'] = base64.b64encode(encrypted[0]).decode('utf-8')
            data_to_decrypt['nonce'] = base64.b64encode(encrypted[1]).decode('utf-8')
            data_to_decrypt['tag'] = base64.b64encode(encrypted[2]).decode('utf-8')
            api_connection = requests.post(data['listenerUrl'] + "/app/process.php?r=api_get_results", json=data_to_decrypt, headers=headers)
            print(api_connection.content)
        else:
            print("An error occured when transfering the file!")

        return True
    except Exception as exception:
        print(f"EXCEPTION | lib.py: send(): {exception}")
