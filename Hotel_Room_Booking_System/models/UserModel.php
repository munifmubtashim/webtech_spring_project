<?php



function registerUser($connection, $name, $email, $passwordHash, $phone, $nationality)
{
    $sql = "INSERT INTO users (name, email, password_hash, phone, nationality, role)
            VALUES (?, ?, ?, ?, ?, 'guest')";

    $statement = $connection->prepare($sql);
    $statement->bind_param("sssss", $name, $email, $passwordHash, $phone, $nationality);
    $result = $statement->execute();

    if ($result)
    {
        return "User Registered Successfully";
    }
    else
    {
        return false;
    }
}


function getUserByEmail($connection, $email)
{
    $sql = "SELECT * FROM users WHERE email = ?";

    $statement = $connection->prepare($sql);
    $statement->bind_param("s", $email);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}


function getUserById($connection, $userId)
{
    $sql = "SELECT * FROM users WHERE id = ?";

    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $userId);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}


function updateProfile($connection, $userId, $name, $phone, $nationality, $specialRequests, $preferredRoomTypeId)
{
    $sql = "UPDATE users
            SET name = ?, phone = ?, nationality = ?,
                special_requests = ?, preferred_room_type_id = ?
            WHERE id = ?";

    $statement = $connection->prepare($sql);
    $statement->bind_param("ssssi i", $name, $phone, $nationality, $specialRequests, $preferredRoomTypeId, $userId);
    $result = $statement->execute();

    if ($result)
    {
        return "Profile Updated Successfully";
    }
    else
    {
        return false;
    }
}


function saveRememberToken($connection, $userId, $token)
{
    $sql = "UPDATE users SET remember_token = ? WHERE id = ?";

    $statement = $connection->prepare($sql);
    $statement->bind_param("si", $token, $userId);
    $statement->execute();
}


function getUserByToken($connection, $token)
{
    $sql = "SELECT * FROM users WHERE remember_token = ?";

    $statement = $connection->prepare($sql);
    $statement->bind_param("s", $token);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}


function clearRememberToken($connection, $userId)
{
    $sql = "UPDATE users SET remember_token = NULL WHERE id = ?";

    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $userId);
    $statement->execute();
}