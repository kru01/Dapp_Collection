package com.example.demo;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.web.bind.annotation.RestController;

@SpringBootApplication
@RestController
public class Application implements QuanLyNhanVien {

	public static void main(String[] args) {
		SpringApplication.run(Application.class, args);
	}

	@Override
	public String LayThongTinNhanVien() {
		return "This is employee information";
	}

	@Override
	public void ThemNhanVien() {
	}
}
