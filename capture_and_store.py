import cv2
import numpy as np
import mysql.connector
from mysql.connector import Error
import time
from datetime import datetime

# Tabel kategori populasi berdasarkan nilai hex warna
population_categories = {
    "> 597": ["#2b221d"],
    "596-457": ["#302521"],
    "456-311": ["#3a291f"],
    "310-267": ["#3d2b21"],
    "266-202": ["#503725"],
    "201-160": ["#754e27"],
    "159-134": ["#7c5029"],
    "133-108": ["#b58231"],
    "107-78": ["#e2c44a"],
    "< 78": ["#f2d74a"]
}

def get_hex_color(frame, x, y):
    b, g, r = frame[y, x]
    return "#{:02x}{:02x}{:02x}".format(r, g, b)

def get_population_category(hex_color):
    closest_category = None
    min_distance = float('inf')

    for category, colors in population_categories.items():
        for color in colors:
            # Hitung jarak warna dengan metode Euclidean
            b1, g1, r1 = int(hex_color[5:7], 16), int(hex_color[3:5], 16), int(hex_color[1:3], 16)
            b2, g2, r2 = int(color[5:7], 16), int(color[3:5], 16), int(color[1:3], 16)
            distance = np.sqrt((r1 - r2) ** 2 + (g1 - g2) ** 2 + (b1 - b2) ** 2)

            if distance < min_distance:
                min_distance = distance
                closest_category = category

    return closest_category

def capture_image():
    cap = cv2.VideoCapture(0)
    ret, frame = cap.read()

    if ret:
        return frame
    else:
        return None

def save_to_database(image_data, hex_color, population_category):
    try:
        connection = mysql.connector.connect(
            host='localhost',
            database='picture',
            user='root',
            password=''
        )

        if connection.is_connected():
            cursor = connection.cursor()

            # Mengonversi gambar (frame) ke dalam format byte array (blob)
            image_blob = cv2.imencode('.jpg', image_data)[1].tobytes()

            # Tambahkan teks dan pointer ke dalam gambar
            color_rgb = tuple(int(hex_color.lstrip('#')[i:i+2], 16) for i in (0, 2, 4))
            cv2.putText(image_data, hex_color, (10, 30), cv2.FONT_HERSHEY_SIMPLEX, 1, (255, 255, 255), 2)
            cv2.rectangle(image_data, (10, 40), (60, 90), color_rgb, -1)  # Kotak warna
            cv2.line(image_data, (image_data.shape[1] // 2, image_data.shape[0] // 2 - 10),
                     (image_data.shape[1] // 2, image_data.shape[0] // 2 + 10), color_rgb, 2)
            cv2.line(image_data, (image_data.shape[1] // 2 - 10, image_data.shape[0] // 2),
                     (image_data.shape[1] // 2 + 10, image_data.shape[0] // 2), color_rgb, 2)

           # Tambahkan informasi populasi ke dalam gambar
            population_text = f"Population: {population_category}"
            cv2.putText(image_data, population_text, (image_data.shape[1] - 200, 30), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 0, 255), 1)

            # Tambahkan informasi waktu pengambilan ke dalam gambar
            capture_time_text = f"Waktu Pengambilan: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}"
            cv2.putText(image_data, capture_time_text, (10, image_data.shape[0] - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (255, 255, 255), 1)

            # Simpan gambar ke dalam database
            insert_query = "INSERT INTO images (image_data, capture_time, hex_color, population_category) VALUES (%s, NOW(), %s, %s)"
            data = (cv2.imencode('.jpg', image_data)[1].tobytes(), hex_color, population_category)

            cursor.execute(insert_query, data)
            connection.commit()

            cursor.close()
            connection.close()
            return True
        else:
            return False

    except Error as e:
        print(f"Error: {e}")
        return False


def main():
    while True:
        try:
            connection = mysql.connector.connect(
                host='localhost',
                database='picture',
                user='root',
                password=''
            )

            if connection.is_connected():
                cursor = connection.cursor()

                # Periksa status "Take Picture" dari database
                cursor.execute("SELECT command FROM camera_command")
                result = cursor.fetchone()

                if result is not None:
                    command = result[0]

                    if command == 'true':
                        # Ambil gambar dari kamera
                        captured_image = capture_image()

                        if captured_image is not None:
                            # Ambil warna di tengah gambar
                            height, width, _ = captured_image.shape
                            x = width // 2
                            y = height // 2
                            hex_color = get_hex_color(captured_image, x, y)

                            # Tentukan populasi kategori
                            population_category = get_population_category(hex_color)

                            # Simpan gambar ke dalam database
                            if save_to_database(captured_image, hex_color, population_category):
                                print("Gambar berhasil disimpan ke database.")
                            else:
                                print("Gagal menyimpan gambar ke database.")

                            # Set status "Take Picture" kembali menjadi "false"
                            cursor.execute("UPDATE camera_command SET command = 'false'")
                            connection.commit()

                cursor.close()
                connection.close()

            else:
                print("Koneksi database gagal.")

            time.sleep(5)
        except Error as e:
            print(f"Error: {e}")

if __name__ == "__main__":
    main()
