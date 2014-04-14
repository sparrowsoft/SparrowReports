<?php

namespace Reports\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Reports\UserBundle\Entity\Users;

class UserController extends Controller
{
    public function getUserData($id) {
        $em = $this->getDoctrine()->getManager();
        $user_data = $em->getRepository('ReportsUserBundle:Users')->find($id);
        
        return $user_data;
    }
    
    public function setUserData($data, $id, $request) {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        
        $username = $em->getRepository('ReportsUserBundle:Users')->findOneBy(array(
            'email' => $data['username'],
        ));
        
        if ( count($username) > 0 ) {
            if ( $username->id != $id ) {
                return 2;
            } 
        }
        
        else {
            $update = $em->getConnection()->prepare(
                "UPDATE users SET email = '" . $data['username'] . "' WHERE id = '" . $id. "'"
            );
                
            $update->execute();
        }
        
        if ( $data['image'] != NULL ) {
            $file = uniqid() . $data['image']->getClientOriginalName();

            $this->generate_thumb($data['image'], 'uploads/' . $file, 100, 100);
            
            $update = $em->getConnection()->prepare(
                "UPDATE users SET image = '" . $file . "' WHERE id = '" . $id . "'"
            );
                
            $update->execute();
            
            $session->set('user_avatar', $file);
        }
        
        if ( $data['password'] != NULL ) {
            if ( $data['password'] != $data['repeat_password'] ) {
                return 3;
            }
            
            else {
                $user = new Users();
                $password = $user->hashPassword($data['password']);
                
                $update = $em->getConnection()->prepare(
                    "UPDATE users SET password = '" . $password . "' WHERE id = '" . $id . "'"
                );
                    
                $update->execute();
            }
        } 
        
        $update = $em->getConnection()->prepare(
                "UPDATE users
                SET first_name = '" . $data['first_name'] . "', 
                last_name = '" . $data['last_name'] ."', 
                role = '" . $data['role'] . "'
                WHERE id = '" . $id. "'"
            );

        $update->execute();

        $session->set('user_first_name', $data['first_name']);
        $session->set('user_last_name', $data['last_name']);
        $session->set('user_role', $data['role']);
        
        return 1;
    }
    
    public function generate_thumb($source_image_path, $thumbnail_image_path, $max_width, $max_height) {
        list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
        
        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                $source_gd_image = imagecreatefromgif($source_image_path);
                break;
            case IMAGETYPE_JPEG:
                $source_gd_image = imagecreatefromjpeg($source_image_path);
                break;
            case IMAGETYPE_PNG:
                $source_gd_image = imagecreatefrompng($source_image_path);
                break;
        }
        
        if ($source_gd_image === false) {
            return false;
        }
        
        $source_aspect_ratio = $source_image_width / $source_image_height;
        $thumbnail_aspect_ratio = $max_width / $max_height;
        
        if ($source_image_width <= $max_width && $source_image_height <= $max_height) {
            $thumbnail_image_width = $source_image_width;
            $thumbnail_image_height = $source_image_height;
        } 
        
        elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
            $thumbnail_image_width = (int) ($max_width * $source_aspect_ratio);
            $thumbnail_image_height = $max_height;
        } 
        
        else {
            $thumbnail_image_width = $max_width;
            $thumbnail_image_height = (int) ($max_height / $source_aspect_ratio);
        }
       
        $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
        
        imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
        imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 100);
        imagedestroy($source_gd_image);
        imagedestroy($thumbnail_gd_image);
        
        return true;
    }
    
    public function getAllUsers() {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('ReportsUserBundle:Users')->findAll();
        
        return $users;
    }
}
