import sys
import json
import os
from paddleocr import PaddleOCR

def main():
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No image path provided"}))
        return

    image_path = sys.argv[1]
    
    if not os.path.exists(image_path):
        print(json.dumps({"error": f"File not found: {image_path}"}))
        return

    try:
        # use_angle_cls=True helps with rotated card photos
        ocr = PaddleOCR(use_angle_cls=True, lang='en', show_log=False)
        result = ocr.ocr(image_path, cls=True)

        lines = []
        if result and result[0]:
            for line in result[0]:
                lines.append(line[1][0]) # line[1][0] is the text string

        print(json.dumps({
            "success": True,
            "raw_text": " ".join(lines)
        }))

    except Exception as e:
        print(json.dumps({
            "success": False, 
            "error": str(e)
        }))

if __name__ == "__main__":
    main()