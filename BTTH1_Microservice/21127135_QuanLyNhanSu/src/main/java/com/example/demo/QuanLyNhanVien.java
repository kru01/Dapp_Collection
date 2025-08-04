package com.example.demo;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;

public interface QuanLyNhanVien {

	@GetMapping(value = "/employee")
	String LayThongTinNhanVien();
	
	@RequestMapping(value = "/add")
	void ThemNhanVien();
}
